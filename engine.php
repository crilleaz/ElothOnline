<?php
require_once __DIR__ . '/vendor/autoload.php';

$db = include 'db.php';

if (isset($_GET['ban'])) {
    if ($player->isAdmin()) {
        $userToBan = $_GET['ban'];
        $db->query("UPDATE users set banned = 1 WHERE anv = '{$userToBan}'");
        $db->query("INSERT INTO chat (username, messages) VALUES ('System', 'User \"{$_GET['ban']}\" has been banned.')");
    } else {
        echo '<center><font color="red">Sorry, you\'re not admin.</font></center>';
    }
}

function getMonsterName($monsterId)
{
    global $db;

    $result = mysqli_query($db, "SELECT name FROM monster WHERE id = {$monsterId}");
    while ($row = mysqli_fetch_array($result)) {
        $monsterName = $row['name'];

        return $monsterName;
    }
}

function getMonsterExp($monsterId)
{
    global $db;

    $result = mysqli_query($db, "SELECT experience FROM monster WHERE monster_id = $monsterId");
    while ($row = mysqli_fetch_array($result)) {
        $monsterExperience = $row['experience'];

        return $monsterExperience;
    }
}

