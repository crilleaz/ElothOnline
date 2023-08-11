<?php
declare(strict_types=1);

namespace Game\Engine;

use Game\Dungeon\DropRepository;
use Game\Dungeon\RewardCalculator;
use Game\Game;
use Game\Item\Item;
use Game\Item\ItemId;
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

        $this->db->transaction(fn () => $this->giveRewards());
        $this->tickGameTime();

        $logs = $this->logs;
        $this->logs = [];

        return $logs;
    }

    private function tickGameTime(): void
    {
        $this->logs[] = '<Timestamp>';

        $somebodyIsHunting = false;

        foreach ($this->getHunters() as $row) {
            // echo 'Some players hunting.';
            $somebodyIsHunting = true;
            $playerNames = $row['username'];
            $timestamp = strtotime($row['tid']);


            // Calculate the difference between the current time and the target timestamp
            $minutes_past = (int)floor((time() - $timestamp) / 60);

            // If the difference is greater than or equal to one minute, add 10 points to the user's score and update the timestamp
            if ($minutes_past > 0) {
                // Update the user's score and timestamp in the database
                $this->db->execute("UPDATE hunting SET tid = NOW() WHERE username = '$playerNames'");
                $this->db->execute("UPDATE players SET stamina = GREATEST(stamina - $minutes_past, 0)  WHERE name = '$playerNames'");
                $this->logs[] = '[resetTimestamp] ' . 'User: ' . $playerNames . ' had their timestamp resetted.' . PHP_EOL;
                $this->logs[] = '[reduceStamina] ' . 'User: ' . $playerNames . ' had their stamina reduced by ' . $minutes_past . PHP_EOL;
            }
        }

        if (!$somebodyIsHunting) {
            $this->logs[] = 'Nobody hunting.';
        }

        $this->stopExhaustedHunters();
        $this->regenerateStamina();
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
            // echo 'spelare med ingen stamina: ' . $playerNameWithNoStamina;
            // echo 'ingen stamina: ' . $playerNameWithNoStamina;

            // echo $row['stamina'];
            $this->db->execute("DELETE from hunting WHERE username = '$playerNameWithNoStamina'");
            $this->db->execute("UPDATE players SET in_combat = 0, stamina=GREATEST(stamina, 0) WHERE name = '$playerNameWithNoStamina'");
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
            $reward = $this->rewardCalculator->calculate($huntingZone, $hunter, $timeSpentInDungeon);

            $hunter->addExp($reward->exp);
            // TODO likely has to be performed within Player::addExp
            $this->playerLog->add($playerName, "[Dungeon] You gained $reward->exp experience points.");
            $this->logs[] = '[giveExperience] ' . 'User: ' . $playerName . ' were given ' . $reward->exp . ' exp' . PHP_EOL;

            foreach ($reward->items as $drop) {
                $hunter->pickUp($drop);
                $this->logs[] = sprintf('[Loot] User: %s were given, %s', $playerName, $drop->item->name) . PHP_EOL;
            }
        }
    }

    /**
     * @return iterable<array{tid: int, username: string, dungeon_id: int}>
     */
    private function getHunters(): iterable
    {
        return $this->db->fetchRows("SELECT username, tid, dungeon_id FROM hunting");
    }
}