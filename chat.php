<?php
$db = include 'db.php';
        // Check if the form has been submitted
        if (isset($_POST['message'])) {
            // Escape the input to protect against SQL injection attacks
            // $name = $db->real_escape_string($_POST['name']);
            if($_POST['message'] != NULL){
                $message = $db->real_escape_string($_POST['message']);
                // Insert the message into the database
                $db->query("INSERT INTO chat (username, messages) VALUES ('$username', '$message')");
                header('Location: index.php');
            }
        }
        // Connect to the database and retrieve the latest messages
        if($_SESSION['username'] == "crilleaz" || $_SESSION['username'] == "GM Crille"){
            getAdminChat();
        }else{
            getNormalChat();
        }
        function getNormalChat(){
            global $db;
            $result = $db->query("(select * from chat order by id desc limit 10) order by id; ");
            // Print the messages
                while ($row = $result->fetch_assoc()) {
                    if($row['username'] == "GM Crille" || $row['username'] == "System" || $row['username'] == "GM Crille"){
                        $dt = new \DateTime($row['tid']);
                        $tid = $dt->format('H:m:s');
                        echo '<div id="chat">';
                        echo "[$tid]" . ' ' . '<font color="red">' . "[{$row['username']}]:" . '</font>' . ' ' . htmlspecialchars($row['messages']) . '<br>';
                        echo '</div>';
                        }else{
                            $dt = new \DateTime($row['tid']);
                            $tid = $dt->format('H:m:s');
                            echo '<div id="chat">';
                            echo "[$tid]" . ' ' .  "{$row['username']}:" . ' ' . htmlspecialchars($row['messages']) . '<br>';
                            echo '</div>';
                        }
                    }
        }

        function getAdminChat(){
            global $db;
            $result = $db->query("(select * from chat order by id desc limit 10) order by id; ");
            // Print the messages
                while ($row = $result->fetch_assoc()) {
                    $userID = $row['username'];
                    if($row['username'] == "crilleaz" || $row['username'] == "System" || $row['username'] == "GM Crille"){
                        $dt = new \DateTime($row['tid']);
                        $tid = $dt->format('H:m:s');
                        echo '<div id="chat">';
                        echo "[$tid]" . ' ' . '<font color="red">' . "[{$row['username']}]:" . '</font>' . ' ' . htmlspecialchars($row['messages']) . '<br>';
                        echo '</div>';
                        }else{
                            $dt = new \DateTime($row['tid']);
                            $tid = $dt->format('H:m:s');
                            echo '<div id="chat">';
                            echo "[$tid]" . ' ' . '<a href="?ban=' . $row['username'] . '">' . "{$row['username']}:" . '</a>' . ' ' . htmlspecialchars($row['messages']) . '<br>';
                            echo '</div>';
                        }
                    }
        }

            ?>