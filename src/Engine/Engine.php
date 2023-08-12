<?php
declare(strict_types=1);

namespace Game\Engine;

use Game\Dungeon\DropRepository;
use Game\Dungeon\RewardCalculator;
use Game\Game;
use Game\Player\PlayerLog;
use Game\Wiki;

class Engine
{
    /**
     * @TODO law of Demeter violated. Access to player log has to be performed in a more suitable place.
     */
    public readonly PlayerLog $playerLog;

    private readonly Wiki $wiki;

    private array $logs = [];

    private readonly RewardCalculator $rewardCalculator;


    public function __construct(private readonly DBConnection $db)
    {
        $this->playerLog = new PlayerLog($this->db);
        $this->wiki = new Wiki($this->db);
        $this->rewardCalculator = new RewardCalculator(new DropRepository($this->db));
    }

    /**
     * @return string[] list of logs
     *
     * @warning Currently none of the methods must be called in a different order. They are tightly interconnected.
     */
    public function performTasks(): array
    {
        $this->logs = [];

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
        $this->logs[] = '<AddStamina>';

        // Basically means timestamp when last action was applied. Implied that action is taken only by Engine
        $row = $this->db->fetchRow("SELECT tid FROM timetable WHERE name = 'stamina'");

        // $timestamp = $row['tid'];
        //Gör om timestamp i DB till unix
        $reference_timestamp = strtotime($row['tid']);
        //Hämta lokal tid
        $current_timestamp = time();
        //Runda ner till senaste minut
        $minutes_past = (int)floor(($current_timestamp - $reference_timestamp) / 60);

        // echo $row['stamina'];
        if ($minutes_past > 0) {
            $this->db->execute("UPDATE players SET stamina = LEAST(stamina + $minutes_past, 100) WHERE in_combat = 0");
            $this->db->execute("UPDATE timetable SET tid = NOW()");
            // TODO return log about the player who restored the stamina
            // echo '[addStamina] ' . 'User: ' . $usersNotInCombat . ' were given ' . '1' . ' stamina.' . PHP_EOL;
        }
    }

    private function stopExhaustedHunters(): void
    {
        $this->logs[] = '<Stamina>';

        $huntingPlayers = $this->db->fetchRows("SELECT name, in_combat, stamina FROM players WHERE stamina <= 0 AND in_combat = 1");
        foreach ($huntingPlayers as $row) {
            $playerNameWithNoStamina = $row['name'];

            $this->db->execute('DELETE from hunting WHERE username = ?', [$playerNameWithNoStamina]);
            $this->db->execute('UPDATE players SET in_combat = 0, stamina=0 WHERE name = ?', [$playerNameWithNoStamina]);
            $this->logs[] = '[noStamina] ' . 'User: ' . $playerNameWithNoStamina . ' had no stamina left.';
        }
    }

    private function giveRewards(): void
    {
        $dungeons = [];
        foreach ($this->wiki->getDungeons() as $dungeonWiki) {
            $dungeons[$dungeonWiki->id] = $dungeonWiki;
        }

        foreach ($this->getHunters() as $row) {
            $playerName = $row['username'];
            $hunter = Game::instance()->findPlayer($playerName);
            if ($hunter === null) {
                $this->logs[] = 'Player ' . $playerName . ' does not exist yet is present in hunting list!';

                continue;
            }

            $huntingZone = $dungeons[$row['dungeon_id']];
            $timeSpentInDungeon = new TimeInterval(time() - strtotime($row['tid']));

            $minutesPassed = $timeSpentInDungeon->toMinutes();
            if ($minutesPassed < 1.0) {
                continue;
            }

            $remainingStamina = $hunter->getStamina();
            $overhunt = false;
            // If player spent in dungeon more time than stamina he has, decrease spent time according that amount
            if ($remainingStamina < $minutesPassed) {
                $overhunt = true;
                $timeSpentInDungeon = TimeInterval::fromMinutes($remainingStamina);
                $minutesPassed = $timeSpentInDungeon->toMinutes();
            }
            $minutesPassed = (int) $minutesPassed;

            $reward = $this->rewardCalculator->calculate($huntingZone, $hunter, $timeSpentInDungeon);
            if ($reward->isEmpty()) {
                // No rewards and has been to dungeon more that he could. Leave
                if ($overhunt) {
                    $hunter->leaveDungeon();
                    $this->db->execute('UPDATE players SET stamina = 0  WHERE name = ?', [$playerName]);
                }

                continue;
            }

            $hunter->addExp($reward->exp);
            $this->logs[] = '[giveExperience] ' . 'User: ' . $playerName . ' were given ' . $reward->exp . ' exp' . PHP_EOL;

            foreach ($reward->listDrop() as $drop) {
                $hunter->pickUp($drop);
                $this->logs[] = sprintf('[Loot] Player: %s picked up %d %s', $playerName, $drop->quantity, $drop->item->name) . PHP_EOL;
            }

            $this->db->execute('UPDATE hunting SET tid = NOW() WHERE username = ?', [$playerName]);
            $this->db->execute('UPDATE players SET stamina = GREATEST(stamina - ?, 0)  WHERE name = ?', [$minutesPassed, $playerName]);
            $this->logs[] = '[resetTimestamp] ' . 'User: ' . $playerName . ' had their timestamp reset.';
            $this->logs[] = '[reduceStamina] ' . 'User: ' . $playerName . ' had their stamina reduced by ' . $minutesPassed;
        }
    }

    /**
     * @return iterable<array{tid: int, username: string, dungeon_id: int}>
     */
    private function getHunters(): iterable
    {
        return $this->db->fetchRows('SELECT username, tid, dungeon_id FROM hunting WHERE tid < NOW() + INTERVAL 1 MINUTE');
    }
}