<?php
session_start();
$db = mysqli_connect("localhost", "user", "password", "db");


// Global variabler
$username = $_SESSION['username'];
// ini_set('short_open_tag', 'On');

function getUsername(){
global $db;
global $username;

    $result = mysqli_query($db,"SELECT name FROM players WHERE name = '{$username}'");
    while($row = mysqli_fetch_array($result)){
    $playerName = $row['name'];

        return $playerName;
}
mysqli_close($db);
}

function getPlayerExp(){
    global $db;
    global $username;
    
        $result = mysqli_query($db,"SELECT experience FROM players WHERE name = '{$username}'");
        while($row = mysqli_fetch_array($result)){
        $playerExp = $row['experience'];
    
            return $playerExp;
    }
    mysqli_close($db);
}

function getPlayerStamina(){
    global $db;
    global $username;
    
        $result = mysqli_query($db,"SELECT stamina FROM players WHERE name = '{$username}'");
        while($row = mysqli_fetch_array($result)){
        $playerStamina = $row['stamina'];
    
            return $playerStamina;
    }
    mysqli_close($db);
}

function getPlayerHealthMax(){
    global $db;
    global $username;
    
        $result = mysqli_query($db,"SELECT health_max FROM players WHERE name = '{$username}'");
        while($row = mysqli_fetch_array($result)){
        $playerHealthMax = $row['health_max'];
    
            return $playerHealthMax;
    }
    mysqli_close($db);
}

function getPlayerMagic(){
    global $db;
    global $username;
    
        $result = mysqli_query($db,"SELECT magic FROM players WHERE name = '{$username}'");
        while($row = mysqli_fetch_array($result)){
        $playerMagic = $row['magic'];
    
            return $playerMagic;
    }
    mysqli_close($db);
}

function getPlayerStrength(){
    global $db;
    global $username;
    
        $result = mysqli_query($db,"SELECT strength FROM players WHERE name = '{$username}'");
        while($row = mysqli_fetch_array($result)){
        $playerStrength = $row['strength'];
    
            return $playerStrength;
    }
    mysqli_close($db);
}

function getPlayerDefense(){
    global $db;
    global $username;
    
        $result = mysqli_query($db,"SELECT defense FROM players WHERE name = '{$username}'");
        while($row = mysqli_fetch_array($result)){
        $playerDefense = $row['defense'];
    
            return $playerDefense;
    }
    mysqli_close($db);
}

function getPlayerWoodcutting(){
    global $db;
    global $username;
    
        $result = mysqli_query($db,"SELECT woodcutting FROM players WHERE name = '{$username}'");
        while($row = mysqli_fetch_array($result)){
        $playerWoodcutting = $row['woodcutting'];
    
            return $playerWoodcutting;
    }
    mysqli_close($db);
}

function getPlayerMining(){
    global $db;
    global $username;
    
        $result = mysqli_query($db,"SELECT mining FROM players WHERE name = '{$username}'");
        while($row = mysqli_fetch_array($result)){
        $playerMining = $row['mining'];
    
            return $playerMining;
    }
    mysqli_close($db);
}

function getPlayerGathering(){
    global $db;
    global $username;
    
        $result = mysqli_query($db,"SELECT gathering FROM players WHERE name = '{$username}'");
        while($row = mysqli_fetch_array($result)){
        $playerGathering = $row['gathering'];
    
            return $playerGathering;
    }
    mysqli_close($db);
}

function getPlayerHarvesting(){
    global $db;
    global $username;
    
        $result = mysqli_query($db,"SELECT harvesting FROM players WHERE name = '{$username}'");
        while($row = mysqli_fetch_array($result)){
        $playerHarvesting = $row['harvesting'];
    
            return $playerHarvesting;
    }
    mysqli_close($db);
}

function getPlayerBlacksmith(){
    global $db;
    global $username;
    
        $result = mysqli_query($db,"SELECT blacksmith FROM players WHERE name = '{$username}'");
        while($row = mysqli_fetch_array($result)){
        $playerBlacksmith = $row['blacksmith'];
    
            return $playerBlacksmith;
    }
    mysqli_close($db);
}

function getPlayerHerbalism(){
    global $db;
    global $username;
    
        $result = mysqli_query($db,"SELECT herbalism FROM players WHERE name = '{$username}'");
        while($row = mysqli_fetch_array($result)){
        $playerHerbalism = $row['herbalism'];
    
            return $playerHerbalism;
    }
    mysqli_close($db);
}

function getPlayerGold(){
    global $db;
    global $username;
    
        $result = mysqli_query($db,"SELECT amount FROM inventory WHERE item_id = '1' AND username = '{$username}'");
        while($row = mysqli_fetch_array($result)){
        $playerGold = $row['amount'];
    
            return $playerGold;
    }
    mysqli_close($db);
}

function getPlayerCrystals(){
    global $db;
    global $username;
    
        $result = mysqli_query($db,"SELECT crystals FROM players WHERE name = '{$username}'");
        while($row = mysqli_fetch_array($result)){
        $playerCrystals = $row['crystals'];
    
            return $playerCrystals;
    }
    mysqli_close($db);
}

function getPlayerLogs($num){
    global $db;
    global $username;
    
        $result = mysqli_query($db,"SELECT message FROM log WHERE username = '{$username}' order by tid DESC limit {$num}");
        while($row = mysqli_fetch_array($result)){
        $playerMessage = $row['message'];
    
            echo $playerMessage . '<br>';
    }
    // mysqli_close($db);
}


function getPlayerStatus(){
    global $db;
    global $username;
    
        $result = mysqli_query($db,"SELECT in_combat FROM players WHERE name = '{$username}'");
        while($row = mysqli_fetch_array($result)){
        $playerCombat = $row['in_combat'];
    
            return $playerCombat;
            echo 'status' . $playerCombat;
    }
    mysqli_close($db);
}

function getPlayerId(){
    global $db;
    global $username;
    
        $result = mysqli_query($db,"SELECT id FROM users WHERE anv = '{$username}'");
        while($row = mysqli_fetch_array($result)){
        $playerId = $row['id'];
    
            return $playerId;
    }
    mysqli_close($db);
}


if (isset($_GET['ban'])) {
    if(getUsername() == 'crilleaz'){
        $userToBan = $_GET['ban'];
        $db->query("UPDATE users set banned = 1 WHERE anv = '{$userToBan}'");
        $db->query("INSERT INTO chat (username, messages) VALUES ('System', 'User \"{$_GET['ban']}\" has been banned.')");
    }else{
        echo '<center><font color="red">Sorry, you\'re not admin.</font></center>';
    }
}

function getPlayerInventory(){
    global $db;
    global $username;
    
        $result = mysqli_query($db,"SELECT item_id FROM inventory WHERE username = '{$username}'");
        while($row = mysqli_fetch_array($result)){
        $itemId = $row['item_id'];
    
            echo $itemId;
    }
    mysqli_close($db);
}

function getItemId(){
    global $db;
    
        $result = mysqli_query($db,"SELECT * FROM items");
        while($row = mysqli_fetch_array($result)){
        $itemIds = $row['item_id'];
        $itemName = $row['name'];
    
            echo $itemIds . '<br>';
            echo $itemName . '<br>';
    }
    mysqli_close($db);
}

function getItemName($itemId){
    global $db;
    
        $result = mysqli_query($db,"SELECT name FROM items WHERE item_id = {$itemId}");
        while($row = mysqli_fetch_array($result)){
        $itemName = $row['name'];
    
            echo $itemName . '<br>';
    }
    // mysqli_close($db);
}

function getDungeonId(){
    global $db;
    
        $result = mysqli_query($db,"SELECT id FROM dungeons");
        while($row = mysqli_fetch_array($result)){
        $dungeonId = $row['id'];
    
            return $dungeonId;
    }
      mysqli_close($db);
}

function getDungeonName($dungeonId){
    global $db;
    
        $result = mysqli_query($db,"SELECT name FROM dungeons WHERE id = {$dungeonId}");
        while($row = mysqli_fetch_array($result)){
        $dungeonName = $row['name'];
    
            return $dungeonName;
    }
     mysqli_close($db);
}

function getMonsterName($monsterId){
    global $db;
    
        $result = mysqli_query($db,"SELECT name FROM monster WHERE id = {$monsterId}");
        while($row = mysqli_fetch_array($result)){
        $monsterName = $row['name'];
    
            return $monsterName;
    }
     mysqli_close($db);
}

function getMonsterId($monsterId){
    global $db;
    
        $result = mysqli_query($db,"SELECT monster_id FROM monster WHERE monster_id = $monsterId");
        while($row = mysqli_fetch_array($result)){
        $monsterId = $row['monster_id'];
    
            return $monsterId;
    }
     mysqli_close($db);
}

function getMonsterExp($monsterId){
    global $db;
    
        $result = mysqli_query($db,"SELECT experience FROM monster WHERE monster_id = $monsterId");
        while($row = mysqli_fetch_array($result)){
        $monsterExperience = $row['experience'];
    
            return $monsterExperience;
    }
     mysqli_close($db);
}

function getHuntingDungeonId(){
    global $db;
    global $username;
        $result = mysqli_query($db,"SELECT dungeon_id FROM hunting WHERE username = '{$username}'");
        while($row = mysqli_fetch_array($result)){
        $dungeonHuntingId = $row['dungeon_id'];
    
            return $dungeonHuntingId;
    }
     mysqli_close($db);
}

function getHuntingDungeonForStartHunt($getId){
    global $db;
        $result = mysqli_query($db,"SELECT id FROM dungeons WHERE id = '{$getId}'");
        while($row = mysqli_fetch_array($result)){
        $dungeonStartHunting = $row['id'];
            
            return $dungeonStartHunting;
    }
     mysqli_close($db);
}

function startHunting(){
    global $db;
    global $username;
        $result = mysqli_query($db,"SELECT dungeon_id FROM hunting WHERE username = '{$username}'");
        if ($result->num_rows === 0) {
            echo 'You went to hunt.';
            $db->query("INSERT INTO hunting (username, dungeon_id, tid) VALUES ('{$username}', '1', NULL)");
            return true;
        } else {
            echo time();
            echo 'You\'re already hunting.';
            return false;
        }
        
     mysqli_close($db);
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
                    $db->query("UPDATE players SET experience = experience + 100 WHERE name = '{$playerNames}'");
                    $db->query("UPDATE hunting SET tid = NULL WHERE username = '{$playerNames}'");
                }else{
                    // echo 'inte högre';
                }
            }
            return false;
        }
        mysqli_close($db);
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
                    $db->query("UPDATE hunting SET tid = NULL WHERE username = '{$playerNames}'");
                    $db->query("UPDATE players SET stamina = stamina -1 WHERE name = '{$playerNames}'");
                    echo '[resetTimestamp] ' . 'User: ' . $playerNames . ' had their timestamp resetted.' . PHP_EOL;
                    echo '[reduceStamina] ' . 'User: ' . $playerNames . ' had their stamina reduced by 1.' . PHP_EOL;
                }
            }
            return false;
        }
        mysqli_close($db);
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
                if($row['dungeon_id'] == 1){
                    $points_earned = $minutes_past * 30; 
                }elseif($row['dungeon_id'] == 2){
                    $points_earned = $minutes_past * 100;
                }
                              
                // check stamina

                  // Update the user's exp and timestamp in the database
                    $db->query("UPDATE players SET experience = experience + $points_earned WHERE name = '{$playerNames}'");
                    if(mysqli_affected_rows($db) >0 ){
                        $db->query("INSERT INTO log (username, message, tid) VALUES ('{$playerNames}', '[Dungeon] You gained $points_earned experience points.', NULL)");
                    }
                    // $db->query("UPDATE hunting SET tid = NULL WHERE username = '{$playerNames}'");
                    echo '[giveExperience] ' . 'User: ' . $playerNames . ' were given ' . $points_earned . ' exp' . PHP_EOL;
                
            }
            return false;
        }
        mysqli_close($db);
}

function getHuntingPlayers(){
    global $db;

    $result = mysqli_query($db,"SELECT username FROM hunting");
    while($row = mysqli_fetch_array($result)){
        $playersHunting = $row['username'];
        return $playersHunting;
    }
    mysqli_close($db);
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
                    $db->query("INSERT INTO log (username, message, tid) VALUES ('{$playerNames}', '[Dungeon] You looted a dead rat, found $random_tier1_gold gold.', NULL)");
                    echo '[Loot] ' . 'User: ' . $playerNames . ' were given, item1' . PHP_EOL;
                    if ($random_number == 2) {
                        checkIfExist($item = 2, $amount = 1, $worth = 4, $player = $playerNames);
                        $db->query("INSERT INTO log (username, message, tid) VALUES ('{$playerNames}', '[Dungeon] You looted a dead rat, found $amount cheese.', NULL)");
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
        // mysqli_close($db);
}

function getPlayerLevel(){
    global $db;
    global $username;

    $user_experience = getPlayerExp();

    // Select level that corresponds to user's experience points
    $sql = "SELECT level FROM exp_table WHERE experience<=$user_experience ORDER BY level DESC LIMIT 1";
    $result = mysqli_query($db, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        // output data of each row
        while($row = mysqli_fetch_assoc($result)) {
            $user_level = $row['level'];
            $db->query("UPDATE players SET level = $user_level WHERE name = '{$username}'");
        }
    } else {
        echo "Level not found";
    }
    
    return $user_level;

    mysqli_close($db);
}

function getPlayerHealth(){
    global $db;
    global $username;
    
        $result = mysqli_query($db,"SELECT health FROM players WHERE name = '{$username}'");
        while($row = mysqli_fetch_array($result)){
        $playerHealth = $row['health'];
            $amountToAdd = '15';
            $calculateHealth = getPlayerLevel() * $amountToAdd;
            $db->query("UPDATE players SET health_max = $calculateHealth WHERE name = '{$username}'");
            return $playerHealth;
    }
    mysqli_close($db);
}

function getPlayerNeedExp(){
        global $db;

        $expNeeded = getPlayerLevel() + 1;
            $result = mysqli_query($db,"SELECT experience FROM exp_table WHERE level = $expNeeded");
            while($row = mysqli_fetch_array($result)){
            $nextExp = $row['experience'];
        
                return $nextExp;
        }
        mysqli_close($db);
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
                $db->query("UPDATE timetable SET tid = NULL");
            }


    }
}

function db($arg1, $arg2, $arg3, $arg4, $arg5){
    global $db;
    $result = mysqli_query($db,"SELECT $arg1 FROM $arg2 WHERE $arg4 = '{$arg5}'");
    while($row = mysqli_fetch_array($result)){
    $level = $row['level'];
        
        echo $level;
    }
    mysqli_close($db);
}

        //    print_r(getMonsterExp($monsterId = 2));
?>