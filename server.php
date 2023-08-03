<?php
include("engine.php");
$db = include 'db.php';

// cron
echo '<Experience>' . PHP_EOL;
giveExperience2();
echo PHP_EOL;
echo '<Loot>' . PHP_EOL;
giveLoot();
echo PHP_EOL;
echo '<Timestamp>' . PHP_EOL;
resetTimestamp();
echo PHP_EOL;
echo '<Stamina>' . PHP_EOL;
forceStopHuntingWithNoStamina();
echo PHP_EOL;
echo '<AddStamina>' . PHP_EOL;
generateStamina();


echo PHP_EOL;
echo 'Cron executed: ' . date("Y-m-d H:i:s") . PHP_EOL;

?>