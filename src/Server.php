<?php
declare(strict_types=1);

namespace Game;

use Carbon\CarbonImmutable;
use Game\Dungeon\DungeonRepository;
use Game\Dungeon\Monster;
use Game\Dungeon\RewardCalculator;
use Game\Dungeon\TTKCalculator;
use Game\Engine\DBConnection;
use Game\Engine\DbTimeFactory;
use Game\Player\CharacterRepository;
use Game\Player\Player;
use Game\Utils\TimeInterval;

class Server
{
    private CarbonImmutable $currentTime;

    /**
     * @var string[]
     */
    private array $logs = [];

    public function __construct(
        private readonly DBConnection $db,
        private readonly TTKCalculator $ttkCalculator,
        private readonly RewardCalculator $rewardCalculator,
        private readonly DungeonRepository $dungeonRepository,
        private readonly CharacterRepository $characterRepository
    ){}

    /**
     * @return string[] list of logs
     *
     * @warning Currently none of the methods must be called in a different order. They are tightly interconnected.
     */
    public function performTasks(): array
    {
        $this->currentTime = CarbonImmutable::now();

        $this->logs = [];
        $this->logs[] = sprintf('<StartedAt>%s</StartedAt>', $this->currentTime->format("H:i:s d-m-Y"));

        $this->db->transaction(function () {
            $this->regenerateStamina();
            $this->giveRewards();
            $this->stopExhaustedHunters();
        });

        $logs = $this->logs;
        $this->logs = [];

        return $logs;
    }

    // Regenerates resting hunters stamina
    private function regenerateStamina(): void
    {
        // Basically means timestamp when last action was applied. Implied that action is taken only by Engine
        $row = $this->db->fetchRow("SELECT tid FROM timetable WHERE name = 'stamina'");

        $lastUpdateAt = DbTimeFactory::fromTimestamp($row['tid']);
        $minutesPassed = $this->currentTime->diffInMinutes($lastUpdateAt);
        if ($minutesPassed === 0) {
            return;
        }

        $this->logs[] = sprintf('<RestoredStamina>%d</RestoredStamina>', min($minutesPassed, Player::MAX_POSSIBLE_STAMINA));

        // TODO stop regenerating stamina for players with activities. Introduce playerState instead of in_combat
        $this->db->execute(
            'UPDATE players SET stamina = LEAST(stamina + ?, ?) WHERE in_combat = 0 AND stamina < ?',
            [$minutesPassed, Player::MAX_POSSIBLE_STAMINA, Player::MAX_POSSIBLE_STAMINA]
        );
        $this->db->execute("UPDATE timetable SET tid = NOW() WHERE name='stamina'");
    }

    private function stopExhaustedHunters(): void
    {
        $huntingPlayers = $this->db->fetchRows("SELECT id, name, in_combat, stamina FROM players WHERE stamina <= 0 AND in_combat = 1");
        foreach ($huntingPlayers as $row) {
            $playerNameWithNoStamina = $row['name'];

            $this->db->execute('DELETE from hunting WHERE character_id = ?', [$row['id']]);
            $this->logs[] = sprintf('<Exhausted player="%s"/>', $playerNameWithNoStamina);
        }

        $this->db->execute('UPDATE players SET in_combat = 0, stamina = 0 WHERE stamina <= 0 AND in_combat = 1');
    }

    private function giveRewards(): void
    {
        $logTemplate = <<<XML
                <Reward player="%s" dungeon="%s" huntDuration="%s">
                    <Killed>%d %s</Killed>
                    <Exp>%d</Exp>
                    <Loot>%s</Loot>
                    <Note>%s</Note>
                </Reward>
        XML;

        $rewardLogs[] = '<Rewards>';
        foreach ($this->getHunters() as $row) {
            $hunter = $this->characterRepository->getById($row['character_id']);
            $huntingZone = $this->dungeonRepository->getById($row['dungeon_id']);

            $lastCheckedAt = DbTimeFactory::fromTimestamp($row['checked_at']);
            $lastRewardedAt = DbTimeFactory::fromTimestamp($row['last_reward_at']);

            $minutesSinceLastCheck = $lastCheckedAt->diffInMinutes($this->currentTime, false);
            if ($minutesSinceLastCheck < 1) {
                continue;
            }

            $minutesSinceLastReward = $lastRewardedAt->diffInMinutes($this->currentTime, false);
            $remainingStamina = $hunter->getStamina();
            // If player spent in dungeon more time than stamina he has, decrease spent time according that amount
            if ($remainingStamina < $minutesSinceLastCheck) {
                $minutesSinceLastCheck = $remainingStamina;
                $minutesSinceLastReward = $lastRewardedAt->diffInMinutes($lastCheckedAt, false) + $remainingStamina;
            }

            $this->db->execute('UPDATE players SET stamina = GREATEST(stamina - ?, 0)  WHERE id = ?', [$minutesSinceLastCheck, $hunter->getId()]);

            ['unitsKilled' => $unitsKilled, 'withinTime' => $timeSpent] = $this->calculateKillCount($hunter, $huntingZone->inhabitant, TimeInterval::fromMinutes($minutesSinceLastReward));

            if ($unitsKilled < 1) {
                $this->db->execute('UPDATE hunting SET checked_at = ? WHERE character_id = ?', [DbTimeFactory::createTimestamp($this->currentTime), $hunter->getId()]);

                continue;
            }

            $reward = $this->rewardCalculator->calculateForHuntedMonster($huntingZone->inhabitant, $unitsKilled);

            $rewardedAt = $lastRewardedAt->addSeconds($timeSpent->seconds);

            $this->db->execute('UPDATE hunting SET checked_at = ?, last_reward_at = ? WHERE character_id = ?', [DbTimeFactory::createTimestamp($this->currentTime), DbTimeFactory::createTimestamp($rewardedAt), $hunter->getId()]);

            $hunter->addExp($reward->exp);
            $lootDetails = '';
            foreach ($reward->items as $item) {
                $hunter->pickUp($item);
                $lootDetails .= sprintf('<Item name="%s" quantity="%d"/>', $item->name, $item->quantity);
            }

            $rewardLogs[] = sprintf(
                $logTemplate,
                $hunter->getName(),
                $huntingZone->name,
                $minutesSinceLastReward . 'minutes',
                $unitsKilled,
                $huntingZone->inhabitant->name,
                $reward->exp,
                $lootDetails,
                'Stamina reduced by ' . $minutesSinceLastCheck
            );
        }

        $rewardLogs[] = '</Rewards>';

        if (count($rewardLogs) !== 2) {
            foreach ($rewardLogs as $rewardLog) {
                $this->logs[] = $rewardLog;
            }
        }

        $this->giveActivityRewards();
    }

    private function giveActivityRewards(): void
    {
        $lastCheckedAt = $this->currentTime->subMinute();

        $activities = $this->db->fetchRows('SELECT character_id, last_reward_at, checked_at FROM activity WHERE checked_at < ?', [DbTimeFactory::createTimestamp($lastCheckedAt)]);

        foreach ($activities as $data) {
            $character = $this->characterRepository->getById($data['character_id']);
            $activity = $character->getCurrentActivity();
            if ($activity === null) {
                $this->logs[] = sprintf('<Error>Character %s expected to have activity but it is missing</Error>', $character->getName());
                continue;
            }

            $lastCheckedAt = DbTimeFactory::fromTimestamp($data['checked_at']);
            $lastRewardedAt = DbTimeFactory::fromTimestamp($data['last_reward_at']);

            $minutesSinceLastCheck = $lastCheckedAt->diffInMinutes($this->currentTime, false);
            $minutesSinceLastReward = $lastRewardedAt->diffInMinutes($this->currentTime, false);
            $remainingStamina = $character->getStamina();
            // If player spent in dungeon more time than stamina he has, decrease spent time according that amount
            if ($remainingStamina < $minutesSinceLastCheck) {
                $minutesSinceLastCheck = $remainingStamina;
                $minutesSinceLastReward = $lastRewardedAt->diffInMinutes($lastCheckedAt, false) + $remainingStamina;
            }

            $this->db->execute('UPDATE players SET stamina = GREATEST(stamina - ?, 0)  WHERE id = ?', [$minutesSinceLastCheck, $character->getId()]);
            $this->db->execute('UPDATE activity SET checked_at=? WHERE character_id=?', [DbTimeFactory::createTimestamp($this->currentTime), $character->getId()]);

            if ($minutesSinceLastReward < 60) {
                continue;
            }

            $fullHoursPassed = (int) ($minutesSinceLastReward/60);
            $rewardPerHour = $activity->calculateReward($character);
            $reward = $rewardPerHour->multiply($fullHoursPassed);

            $character->addExp($reward->exp);
            foreach ($reward->items as $item) {
                $character->pickUp($item);
            }

            $lastRewardedAt = $this->currentTime->addHours($fullHoursPassed);
            $this->db->execute('UPDATE activity SET last_reward_at=? WHERE character_id=?', [DbTimeFactory::createTimestamp($lastRewardedAt), $character->getId()]);
        }
    }

    /**
     * @return iterable<array{checked_at: string, last_reward_at: string, character_id: int, dungeon_id: int}>
     */
    private function getHunters(): iterable
    {
        $lastCheckedAt = $this->currentTime->subMinute();

        // TODO change username to id
        return $this->db->fetchRows(
            'SELECT character_id, dungeon_id, last_reward_at, checked_at FROM hunting WHERE checked_at < ?',
            [DbTimeFactory::createTimestamp($lastCheckedAt)]
        );
    }

    /**
     * @param Player $hunter
     * @param Monster $monster
     *
     * @return array{unitsKilled: int, withinTime: TimeInterval}
     */
    private function calculateKillCount(Player $hunter, Monster $monster, TimeInterval $withinTime): array
    {
        $ttkMonster = $this->ttkCalculator->calculate($hunter, $monster);
        $ttkPlayer = $this->ttkCalculator->calculateForMonster($monster, $hunter);

        // If player needs more time to kill monster than monsters needs to kill player
        if ($ttkMonster->isGreaterThan($ttkPlayer)) {
            return ['unitsKilled' => 0, 'withinTime' => $withinTime];
        }

        $unitsKilled = (int)floor($withinTime->toMinutes() / $ttkMonster->toMinutes());
        if ($unitsKilled === 0) {
            return ['unitsKilled' => 0, 'withinTime' => $withinTime];
        }

        return [
            'unitsKilled' => $unitsKilled,
            'withinTime' => new TimeInterval($ttkMonster->seconds * $unitsKilled),
        ];
    }
}
