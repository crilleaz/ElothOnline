<?php
declare(strict_types=1);

namespace Game;

use Carbon\CarbonImmutable;
use Game\Dungeon\DungeonRepository;
use Game\Dungeon\RewardCalculator;
use Game\Engine\DBConnection;
use Game\Engine\DbTimeFactory;
use Game\Player\CharacterRepository;
use Game\Player\Player;

class Server
{
    private CarbonImmutable $currentTime;

    private array $logs = [];

    public function __construct(
        private readonly DBConnection $db,
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

        $lastUpdateAt = CarbonImmutable::create($row['tid']);
        $minutesPassed = $this->currentTime->diffInMinutes($lastUpdateAt);
        if ($minutesPassed === 0) {
            return;
        }

        $this->logs[] = sprintf('<RestoredStamina>%d</RestoredStamina>', $minutesPassed);
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
            $this->db->execute('UPDATE players SET in_combat = 0, stamina=0 WHERE id = ?', [$row['id']]);
            $this->logs[] = sprintf('<Exhausted player="%s"/>', $playerNameWithNoStamina);
        }
    }

    private function giveRewards(): void
    {
        $logTemplate = <<<XML
                <Reward player="%s" dungeon="%s" huntDuration="%s">
                    <Exp>%d</Exp>
                    <Loot>%s</Loot>
                    <Note>%s</Note>
                </Reward>
        XML;

        $rewardLogs[] = '<Rewards>';
        $now = CarbonImmutable::now();
        foreach ($this->getHunters() as $row) {
            $hunter = $this->characterRepository->getById($row['character_id']);
            $huntingZone = $this->dungeonRepository->getById($row['dungeon_id']);

            $lastCheckedAt = CarbonImmutable::create($row['checked_at']);
            $lastRewardedAt = CarbonImmutable::create($row['last_reward_at']);

            $minutesSinceLastCheck = $lastCheckedAt->diffInMinutes($now, false);
            if ($minutesSinceLastCheck < 1) {
                continue;
            }

            $minutesSinceLastReward = $lastRewardedAt->diffInMinutes($now, false);
            $remainingStamina = $hunter->getStamina();
            // If player spent in dungeon more time than stamina he has, decrease spent time according that amount
            if ($remainingStamina < $minutesSinceLastCheck) {
                $minutesSinceLastCheck = $remainingStamina;
                $minutesSinceLastReward = $lastRewardedAt->diffInMinutes($lastCheckedAt, false) + $remainingStamina;
            }

            $this->db->execute('UPDATE players SET stamina = GREATEST(stamina - ?, 0)  WHERE id = ?', [$minutesSinceLastCheck, $hunter->getId()]);

            $reward = $this->rewardCalculator->calculate($huntingZone, $hunter, $minutesSinceLastReward);
            if ($reward->isEmpty()) {
                $this->db->execute('UPDATE hunting SET checked_at = ? WHERE character_id = ?', [DbTimeFactory::createTimestamp($now), $hunter->getId()]);

                continue;
            }

            $this->db->execute('UPDATE hunting SET checked_at = ?, last_reward_at = ? WHERE character_id = ?', [DbTimeFactory::createTimestamp($now), DbTimeFactory::createTimestamp($now), $hunter->getId()]);

            $hunter->addExp($reward->exp);

            $lootDetails = '';
            foreach ($reward->listDrop() as $drop) {
                $hunter->pickUp($drop);
                $lootDetails .= sprintf('<Item name="%s" quantity="%d"/>', $drop->item->name, $drop->quantity);
            }

            $rewardLogs[] = sprintf(
                $logTemplate,
                $hunter->getName(),
                $huntingZone->name,
                $minutesSinceLastReward . 'minutes',
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

            $lastCheckedAt = CarbonImmutable::create($data['checked_at']);
            $lastRewardedAt = CarbonImmutable::create($data['last_reward_at']);

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
            foreach ($reward->listDrop() as $drop) {
                $character->pickUp($drop);
            }

            $lastRewardedAt = $this->currentTime->addHours($fullHoursPassed);
            $this->db->execute('UPDATE activity SET last_reward_at=? WHERE character_id=?', [DbTimeFactory::createTimestamp($lastRewardedAt), $character->getId()]);
        }
    }

    /**
     * @return iterable<array{checked_at: int, last_reward_at: int, character_id: int, dungeon_id: int}>
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
}
