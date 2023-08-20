<?php
declare(strict_types=1);

namespace Game\Engine;

// TODO contains functions which were likely designed for debugging purposes and thus unused at the moment
class Debug
{

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
}