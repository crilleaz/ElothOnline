<?php
declare(strict_types=1);

namespace Game;

class Engine
{
    /**
     * @TODO law of Demeter violated. Access to player log has to be performed in a more suitable place.
     */
    public PlayerLog $playerLog;

    private array $logs = [];


    public function __construct(private readonly DBConnection $db)
    {
        $this->playerLog = new PlayerLog($this->db);
    }

    /**
     * @return string[] list of logs
     *
     * @warning Currently none of the methods must be called in a different order. They are tightly interconnected.
     */
    public function performTasks(): array
    {
        $this->logs = [];

        $this->giveExperience();
        $this->giveLoot();
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

    private function giveLoot(): void
    {
        $this->logs[] = '<Loot>';

        $somebodyIsHunting = false;
        foreach ($this->getHunters() as $row) {
            // echo 'Some players hunting.';
            $somebodyIsHunting = true;
            $playerNames = $row['username'];
            $player = Player::loadPlayer($playerNames, $this->db);
            $fetch_name = $player->getHuntingDungeon()->name;

            //items_ids
            // 1 = gold worth 0
            // 2 = cheese worth 4

            // Lootable
            // TODO can be issued through strategies. Will greatly simplify new and old rewards
            if ($fetch_name == 'Rat Cave') {
                $random_tier1_gold = rand(2, 7);
                $random_number = rand(1, 2);
                $this->addToInventory(ItemId::GOLD, $random_tier1_gold, 0, $playerNames);

                $this->playerLog->add($playerNames, "[Dungeon] You looted a dead rat, found $random_tier1_gold gold.");
                $this->logs[] = '[Loot] ' . 'User: ' . $playerNames . ' were given, item1' . PHP_EOL;
                if ($random_number == 2) {
                    $amount = 1;
                    $this->addToInventory(ItemId::CHEESE, $amount, 4, $playerNames);
                    $this->playerLog->add($playerNames, "[Dungeon] You looted a dead rat, found $amount cheese.");
                    $this->logs[] = '[Loot] ' . 'User: ' . $playerNames . ' were given, item2';
                }
            } elseif ($fetch_name == 'Rotworm Cave') {
                echo 'rats';
            } elseif ($fetch_name == 'Dragon Lair') {
                //tier2
                $random_tier2_gold = rand(10, 20);
                echo 'Dragon Alir';
            } elseif ($fetch_name == 'Hatchling Cave') {
                echo 'nope';
                //tier3
                $random_tier3_gold = rand(25, 50);
            }
        }

        if (!$somebodyIsHunting) {
            $this->logs[] = 'Nobody hunting.';
        }
    }

    // TODO calculation must take stamina into account. Currently, if there were pauses in cron server.php, 1 stamina player
    // will receive time() - (delay/60) amount of experience.
    private function giveExperience(): void
    {
        $this->logs[] = '<Experience>';

        foreach ($this->getHunters() as $row) {
            $playerNames = $row['username'];
            // TODO likely has to be used to determine exp multiplier (currently hardcoded as 30,100)
            $dungeonId = $row['dungeon_id'];
            // $timestamp = $row['tid'];
            //Gör om timestamp i DB till unix
            $reference_timestamp = strtotime($row['tid']);
            //Hämta lokal tid
            $current_timestamp = time();
            //Runda ner till senaste minut
            $minutes_past = floor(($current_timestamp - $reference_timestamp) / 60);
            //Ge exp
            $points_earned = 0;
            if ($row['dungeon_id'] == 1) {
                $points_earned = $minutes_past * 30;
            } elseif ($row['dungeon_id'] == 2) {
                $points_earned = $minutes_past * 100;
            }

            $points_earned = (int) $points_earned;

            // check stamina

            $hunter = Game::instance()->findPlayer($playerNames);
            if ($hunter === null) {
                $this->logs[] = 'Player ' . $playerNames . ' does not exist yet is present in hunting list!';

                continue;
            }
            // Update the user's exp and timestamp in the database
            $hunter->addExp($points_earned);
            // TODO likely has to be performed within Player::addExp
            $this->playerLog->add($playerNames, "[Dungeon] You gained $points_earned experience points.')");
            $this->logs[] = '[giveExperience] ' . 'User: ' . $playerNames . ' were given ' . $points_earned . ' exp' . PHP_EOL;
        }
    }

    // TODO move to player inventory or something like that. former checkIfExists
    public function addToInventory(ItemId $itemId, $amount, $worth, $player): void
    {
        $item = $itemId->value;

        $entry = $this->db->fetchRow("SELECT amount FROM inventory WHERE item_id = $item AND username = '{$player}'");
        if ($entry === []) {
            $this->logs[] = '[checkIfItemExist] ' . 'Drop for: ' . $player . ' was not found, adding to DB' . PHP_EOL;
            // Användare hade inte i inventory
            $this->db->execute("INSERT INTO inventory (username, item_id, amount, worth) VALUES ('{$player}', '$item', '$amount', '$worth')");
        } else {
            $this->logs[] = '[checkIfItemExist] ' . 'Drop for: ' . $player . ' were found, updating DB' . PHP_EOL;
            // Användaren hade redan i inventory
            $this->db->execute("UPDATE inventory SET amount = amount + $amount WHERE item_id = $item AND username = '$player'");
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