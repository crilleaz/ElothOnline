<?php
declare(strict_types=1);

namespace Game\Engine;

use Carbon\CarbonImmutable;
use Game\Dungeon\RewardCalculator;
use Game\Player\Player;
use Game\Utils\TimeInterval;
use Game\Wiki;

class Engine
{
    private CarbonImmutable $currentTime;

    private array $logs = [];

    public function __construct(
        private readonly DBConnection $db,
        private readonly RewardCalculator $rewardCalculator,
        private readonly Wiki $wiki
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
        $huntingPlayers = $this->db->fetchRows("SELECT name, in_combat, stamina FROM players WHERE stamina <= 0 AND in_combat = 1");
        foreach ($huntingPlayers as $row) {
            $playerNameWithNoStamina = $row['name'];

            $this->db->execute('DELETE from hunting WHERE username = ?', [$playerNameWithNoStamina]);
            $this->db->execute('UPDATE players SET in_combat = 0, stamina=0 WHERE name = ?', [$playerNameWithNoStamina]);
            $this->logs[] = sprintf('<Exhausted player="%s"/>', $playerNameWithNoStamina);
        }
    }

    private function giveRewards(): void
    {
        $dungeons = [];
        foreach ($this->wiki->getDungeons() as $dungeonWiki) {
            $dungeons[$dungeonWiki->id] = $dungeonWiki;
        }

        $logTemplate = <<<XML
                <Reward player="%s" dungeon="%s" huntDuration="%s">
                    <Exp>%d</Exp>
                    <Loot>%s</Loot>
                    <Note>%s</Note>
                </Reward>
        XML;

        $rewardLogs[] = '<Rewards>';
        foreach ($this->getHunters() as $row) {
            $playerName = $row['username'];
            $hunter = Player::loadPlayer($playerName, $this->db);
            $huntingZone = $dungeons[$row['dungeon_id']];

            $timeSpentInDungeon = TimeInterval::between(CarbonImmutable::create($row['tid']), $this->currentTime);
            $minutesPassed = $timeSpentInDungeon->toMinutes();
            if ($minutesPassed < 1.0) {
                continue;
            }

            $remainingStamina = $hunter->getStamina();
            $overhunt = false;
            // If player spent in dungeon more time than stamina he has, decrease spent time according that amount
            if ($remainingStamina < $minutesPassed) {
                $overhunt = true;
                $minutesPassed = $remainingStamina;
                $timeSpentInDungeon = TimeInterval::fromMinutes($minutesPassed);
            }
            $minutesPassed = (int) $minutesPassed;

            $reward = $this->rewardCalculator->calculate($huntingZone, $hunter, $timeSpentInDungeon);
            if ($reward->isEmpty()) {
                // No rewards and has been to dungeon more that he could. Leave
                if ($overhunt) {
                    $hunter->leaveDungeon();
                    $this->db->execute('UPDATE players SET stamina = 0  WHERE name = ?', [$playerName]);
                    $rewardLogs[] = sprintf(trim($logTemplate), $playerName, $huntingZone->name, $minutesPassed . ' minutes', 0, '', 'Exhausted');
                }

                continue;
            }

            $hunter->addExp($reward->exp);

            $lootDetails = '';
            foreach ($reward->listDrop() as $drop) {
                $hunter->pickUp($drop);
                $lootDetails .= sprintf('<Item name="%s" quantity="%d"/>', $drop->item->name, $drop->quantity);
            }

            $this->db->execute('UPDATE hunting SET tid = NOW() WHERE username = ?', [$playerName]);
            $this->db->execute('UPDATE players SET stamina = GREATEST(stamina - ?, 0)  WHERE name = ?', [$minutesPassed, $playerName]);

            $rewardLogs[] = sprintf(
                $logTemplate,
                $playerName,
                $huntingZone->name,
                $minutesPassed . 'minutes',
                $reward->exp,
                $lootDetails,
                'Stamina reduced by ' . $minutesPassed
            );
        }

        $rewardLogs[] = '</Rewards>';

        if (count($rewardLogs) !== 2) {
            foreach ($rewardLogs as $rewardLog) {
                $this->logs[] = $rewardLog;
            }
        }
    }

    /**
     * @return iterable<array{tid: int, username: string, dungeon_id: int}>
     */
    private function getHunters(): iterable
    {
        $startedHuntingBefore = $this->currentTime->subMinute();

        return $this->db->fetchRows(
            'SELECT username, tid, dungeon_id FROM hunting WHERE tid < ?',
            [DbTimeFactory::createTimestamp($startedHuntingBefore)]
        );
    }
}
