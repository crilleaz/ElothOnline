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
