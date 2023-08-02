<?php
require_once __DIR__.'/src/Game.php';

session_start();

$db = include 'db.php';

// Global variabler
try {
    $player = \Game\Game::instance()->getCurrentPlayer();
} catch (Throwable $e) {
    // TODO relevant to src/Game.php::21
    $player = null;
}
// ini_set('short_open_tag', 'On');


function getPlayerLogs($num){
    global $db;
    global $username;
    
        $result = mysqli_query($db,"SELECT message FROM log WHERE username = '{$username}' order by tid DESC limit {$num}");
        while($row = mysqli_fetch_array($result)){
        $playerMessage = $row['message'];
    
            echo $playerMessage . '<br>';
    }
}

if (isset($_GET['ban'])) {
    if($player->isAdmin()){
        $userToBan = $_GET['ban'];
        $db->query("UPDATE users set banned = 1 WHERE anv = '{$userToBan}'");
        $db->query("INSERT INTO chat (username, messages) VALUES ('System', 'User \"{$_GET['ban']}\" has been banned.')");
    }else{
        echo '<center><font color="red">Sorry, you\'re not admin.</font></center>';
    }
}


function getItemName($itemId){
    global $db;
    
        $result = mysqli_query($db,"SELECT name FROM items WHERE item_id = {$itemId}");
        while($row = mysqli_fetch_array($result)){
        $itemName = $row['name'];
    
            echo $itemName . '<br>';
    }
}

function getDungeonId(){
    global $db;
    
        $result = mysqli_query($db,"SELECT id FROM dungeons");
        while($row = mysqli_fetch_array($result)){
        $dungeonId = $row['id'];
    
            return $dungeonId;
    }
}

function getDungeonName($dungeonId){
    global $db;
    
        $result = mysqli_query($db,"SELECT name FROM dungeons WHERE id = {$dungeonId}");
        while($row = mysqli_fetch_array($result)){
        $dungeonName = $row['name'];
    
            return $dungeonName;
    }
}

function getMonsterName($monsterId){
    global $db;
    
        $result = mysqli_query($db,"SELECT name FROM monster WHERE id = {$monsterId}");
        while($row = mysqli_fetch_array($result)){
        $monsterName = $row['name'];
    
            return $monsterName;
    }
}

function getMonsterId($monsterId){
    global $db;
    
        $result = mysqli_query($db,"SELECT monster_id FROM monster WHERE monster_id = $monsterId");
        while($row = mysqli_fetch_array($result)){
        $monsterId = $row['monster_id'];
    
            return $monsterId;
    }
}

function getMonsterExp($monsterId){
    global $db;
    
        $result = mysqli_query($db,"SELECT experience FROM monster WHERE monster_id = $monsterId");
        while($row = mysqli_fetch_array($result)){
        $monsterExperience = $row['experience'];
    
            return $monsterExperience;
    }
}

function getHuntingDungeonId(){
    global $db;
    global $username;
        $result = mysqli_query($db,"SELECT dungeon_id FROM hunting WHERE username = '{$username}'");
        while($row = mysqli_fetch_array($result)){
        $dungeonHuntingId = $row['dungeon_id'];
    
            return $dungeonHuntingId;
    }
}

function getHuntingDungeonForStartHunt($getId){
    global $db;
        $result = mysqli_query($db,"SELECT id FROM dungeons WHERE id = '{$getId}'");
        while($row = mysqli_fetch_array($result)){
        $dungeonStartHunting = $row['id'];
            
            return $dungeonStartHunting;
    }
}

function startHunting(){
    global $db;
    global $username;
        $result = mysqli_query($db,"SELECT dungeon_id FROM hunting WHERE username = '{$username}'");
        if ($result->num_rows === 0) {
            echo 'You went to hunt.';
            $db->query("INSERT INTO hunting (username, dungeon_id) VALUES ('{$username}', '1')");
            return true;
        } else {
            echo time();
            echo 'You\'re already hunting.';
            return false;
        }
}

function giveExperience(){
    global $db;
        $result = mysqli_query($db,"SELECT username, tid FROM hunting");
        if ($result->num_rows === 0) {
            echo 'Nobody hunting.';
            return true;
        } else {
            // echo 'Some players hunting.';
            while($row = mysqli_fetch_array($result)){
                $playerNames = $row['username'];
                $timestamp = $row['tid'];

                // Convert the target timestamp to a Unix timestamp
                $target_timestamp = strtotime($timestamp);
                
                // Calculate the difference between the current time and the target timestamp
                $time_difference = time() - $target_timestamp;
                
                // If the difference is greater than or equal to one minute, add 10 points to the user's score and update the timestamp
                if ($time_difference >= 60) {
                  $timestamp = time();
                  // Update the user's score and timestamp in the database
                    $hunter = \Game\Game::instance()->findPlayer($playerNames);
                    if ($hunter === null) {
                        echo 'Player ' . $playerNames . ' does not exist yet is present in hunting list!';

                        continue;
                    }
                    $hunter->addExp(100);
                    $db->query("UPDATE hunting SET tid = NOW() WHERE username = '{$playerNames}'");
                }else{
                    // echo 'inte högre';
                }
            }
            return false;
        }
}

function resetTimestamp(){
    global $db;
        $result = mysqli_query($db,"SELECT username, tid FROM hunting");
        if ($result->num_rows === 0) {
            echo 'Nobody hunting.';
            return true;
        } else {
            // echo 'Some players hunting.';
            while($row = mysqli_fetch_array($result)){
                $playerNames = $row['username'];
                $timestamp = $row['tid'];

                // Convert the target timestamp to a Unix timestamp
                $target_timestamp = strtotime($timestamp);
                
                // Calculate the difference between the current time and the target timestamp
                $time_difference = time() - $target_timestamp;
                
                // If the difference is greater than or equal to one minute, add 10 points to the user's score and update the timestamp
                if ($time_difference >= 60) {
                  $timestamp = time();
                  // Update the user's score and timestamp in the database
                    $db->query("UPDATE hunting SET tid = NOW() WHERE username = '{$playerNames}'");
                    $db->query("UPDATE players SET stamina = stamina -1 WHERE name = '{$playerNames}'");
                    echo '[resetTimestamp] ' . 'User: ' . $playerNames . ' had their timestamp resetted.' . PHP_EOL;
                    echo '[reduceStamina] ' . 'User: ' . $playerNames . ' had their stamina reduced by 1.' . PHP_EOL;
                }
            }
            return false;
        }
}

//2023-01-02 04:06 slutade här för dagen
//2023-01-03 01:48 verkar som alla blir in_combat = 1 eller 0 när någon joinar/lämnar dungeon
// Ovan fixad


function giveExperience2(){
    global $db;
        $result = mysqli_query($db,"SELECT username, tid, dungeon_id FROM hunting");
        if ($result->num_rows === 0) {
            // echo 'Nobody hunting.';
            return true;
        } else {
            // echo 'Some players hunting.';
            while($row = mysqli_fetch_array($result)){
                $playerNames = $row['username'];
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
                if($row['dungeon_id'] == 1){
                    $points_earned = $minutes_past * 30; 
                }elseif($row['dungeon_id'] == 2){
                    $points_earned = $minutes_past * 100;
                }
                              
                // check stamina

                  // Update the user's exp and timestamp in the database
                $hunter = \Game\Game::instance()->findPlayer($playerNames);
                if ($hunter === null) {
                    echo 'Player ' . $playerNames . ' does not exist yet is present in hunting list!';

                    continue;
                }
                $hunter->addExp($points_earned);
                $db->query("INSERT INTO log (username, message) VALUES ('{$playerNames}', '[Dungeon] You gained $points_earned experience points.')");
                    // $db->query("UPDATE hunting SET tid = NULL WHERE username = '{$playerNames}'");
                    echo '[giveExperience] ' . 'User: ' . $playerNames . ' were given ' . $points_earned . ' exp' . PHP_EOL;
            }
            return false;
        }
}

function getHuntingPlayers(){
    global $db;

    $result = mysqli_query($db,"SELECT username FROM hunting");
    while($row = mysqli_fetch_array($result)){
        $playersHunting = $row['username'];
        return $playersHunting;
    }
}




function checkIfExist($item, $amount, $worth, $player){
    global $db;
    // global $username;

    $result = mysqli_query($db,"SELECT amount FROM inventory WHERE item_id = $item AND username = '{$player}'");
    if ($result->num_rows === 0) {
        echo '[checkIfItemExist] ' . 'Drop for: ' . $player . ' was not found, adding to DB' . PHP_EOL;
        // Användare hade inte i inventory
        $db->query("INSERT INTO inventory (username, item_id, amount, worth) VALUES ('{$player}', '$item', '$amount', '$worth')");
    } else {
        echo '[checkIfItemExist] ' . 'Drop for: ' . $player . ' were found, updating DB' . PHP_EOL;
        // Användaren hade redan i inventory
        $db->query("UPDATE inventory SET amount = amount + $amount WHERE item_id = $item AND username = '$player'");
    }
}


function giveLoot(){
    global $db;
    global $username;
        $result = mysqli_query($db,"SELECT username, tid, dungeon_id FROM hunting");
        if ($result->num_rows === 0) {
            echo 'Nobody hunting.';
            return true;
        } else {
            // echo 'Some players hunting.';
            while($row = mysqli_fetch_array($result)){
                $playerNames = $row['username'];
                $timestamp = $row['tid'];
                $dungeonIds = $row['dungeon_id'];
                $fetch_name = getDungeonName($dungeonId = $row['dungeon_id']);
                // echo $playerNames;
                // random gold
                //tier1
                
                $random_tier1_gold = rand(2, 7);
                //tier2
                $random_tier2_gold = rand(10, 20);
                //tier3
                $random_tier3_gold = rand(25, 50);

                //items_ids
                // 1 = gold worth 0
                // 2 = cheese worth 4

                // Lootable
                if($fetch_name == 'Rat Cave'){
                    $random_number = rand(1, 2);
                    checkIfExist($item = 1, $amount = $random_tier1_gold, $worth = 0, $player = $playerNames);
                    $db->query("INSERT INTO log (username, message) VALUES ('{$playerNames}', '[Dungeon] You looted a dead rat, found $random_tier1_gold gold.')");
                    echo '[Loot] ' . 'User: ' . $playerNames . ' were given, item1' . PHP_EOL;
                    if ($random_number == 2) {
                        checkIfExist($item = 2, $amount = 1, $worth = 4, $player = $playerNames);
                        $db->query("INSERT INTO log (username, message) VALUES ('{$playerNames}', '[Dungeon] You looted a dead rat, found $amount cheese.')");
                        echo '[Loot] ' . 'User: ' . $playerNames . ' were given, item2' . PHP_EOL;
                    }                    
                }elseif($fetch_name == 'Rotworm Cave'){
                    echo 'rats';

                }elseif($fetch_name == 'Dragon Lair'){
                    echo 'Dragon Alir';

                }elseif($fetch_name == 'Hatchling Cave'){
                    echo 'nope';

                }
            }
            return false;
        }
}

function forceStopHuntingWithNoStamina(){
    global $db;
        $result = mysqli_query($db,"SELECT name, in_combat, stamina FROM players WHERE stamina <= 0 AND in_combat = 1");
        while($row = mysqli_fetch_array($result)){
        $playerNameWithNoStamina = $row['name'];
        // echo 'spelare med ingen stamina: ' . $playerNameWithNoStamina;
        // echo 'ingen stamina: ' . $playerNameWithNoStamina;

        if($row['stamina'] <= 0){
            // echo $row['stamina'];
            $db->query("DELETE from hunting WHERE username = '{$playerNameWithNoStamina}'");
            $db->query("UPDATE players SET in_combat = 0 WHERE name = '{$playerNameWithNoStamina}'");
            echo '[noStamina] ' . 'User: ' . $playerNameWithNoStamina . ' had no stamina left.' . PHP_EOL;
        }
    
    }
}

function giveStamina(){
    global $db;

        $result = mysqli_query($db,"SELECT name, in_combat, stamina FROM players WHERE in_combat = 0");
        while($row = mysqli_fetch_array($result)){
        $usersNotInCombat = $row['name'];
        $currentStamina = $row['stamina'];
        
        if($currentStamina < 100){
        $db->query("UPDATE players SET stamina = stamina + 1 WHERE name = '{$usersNotInCombat}'");
        echo '[addStamina] ' . 'User: ' . $usersNotInCombat . ' were given ' . '1' . ' stamina.' . PHP_EOL;
        }
    }
}

function generateStamina(){
    global $db;
        $result = mysqli_query($db,"SELECT tid FROM timetable WHERE name = 'stamina'");
        while($row = mysqli_fetch_array($result)){

        // $timestamp = $row['tid'];
        //Gör om timestamp i DB till unix
        $reference_timestamp = strtotime($row['tid']);
        //Hämta lokal tid
        $current_timestamp = time();
        //Runda ner till senaste minut
        $minutes_past = floor(($current_timestamp - $reference_timestamp) / 60);
        //Ge stamina
        $staminaToAdd = $minutes_past * 1; 

            // echo $row['stamina'];
            if($minutes_past >= 1){
                giveStamina();
                $db->query("UPDATE timetable SET tid = NOW()");
            }


    }
}

// TODO likely leftovers? should be removed due to security reasons
function db($arg1, $arg2, $arg3, $arg4, $arg5){
    global $db;
    $result = mysqli_query($db,"SELECT $arg1 FROM $arg2 WHERE $arg4 = '{$arg5}'");
    while($row = mysqli_fetch_array($result)){
    $level = $row['level'];
        
        echo $level;
    }
}

        //    print_r(getMonsterExp($monsterId = 2));
?>