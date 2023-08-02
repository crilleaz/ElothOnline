<?php
session_start();

$username = $_SESSION['username'];
$db = include 'db.php';
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

getPlayerLogs($num = 5);
?>