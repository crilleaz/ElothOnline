<?php include '_header.php'; ?>
<?php

/** @var \Game\Player $player */

if (isset($_GET['hunt'])) {
    $selectedDungeon = (int)$_GET['hunt'];
    // Escape the input to protect against SQL injection attacks
    if ($selectedDungeon) {
        // GET är INT
        global $db;
        $result = mysqli_query($db, "SELECT username FROM hunting WHERE username = '{$player->getName()}'");
        if ($result->num_rows === 0) {
            //Huntar inte
            $db->query("INSERT INTO hunting (username, dungeon_id) VALUES ('{$player->getName()}', '$selectedDungeon')");
            $db->query("UPDATE players SET in_combat = 1 WHERE name = '{$player->getName()}'");
            echo '<meta http-equiv="Refresh" content="0; url=launcher.php?tab=dungeons&success=hunting">';

        } else {
            //Huntar redan
            echo '<meta http-equiv="Refresh" content="0; url=launcher.php?tab=dungeons&?error=hunting">';
        }
    }
}

if (isset($_GET['leave'])) {
    // Escape the input to protect against SQL injection attacks
    if ((int)$_GET['leave']) {
        // GET är INT
        global $db;
        $result = mysqli_query($db, "SELECT username FROM hunting WHERE username = '{$player->getName()}'");
        if ($result->num_rows === 0) {
            //Huntar inte
            // Behövs inte tas bort
            echo 'huntar inte';
        } else {
            //Huntar redan
            // Tas bort
            $db->query("DELETE from hunting WHERE username = '{$player->getName()}'");
            $db->query("UPDATE players SET in_combat = 0  WHERE name = '{$player->getName()}'");
            echo '<meta http-equiv="Refresh" content="0; url=launcher.php?tab=dungeons&?left=success">';
        }
    }
}

if (isset($_GET['error'])) {
    //your html for error message
    $errorMsg = 'You are already hunting';
} elseif (isset($_GET['success'])) {
    //your html for error message
    $infoMsg = 'You started hunting.';
} elseif (isset($_GET['left'])) {
    //your html for error message
    $infoMsg = 'You left the dungeon';
}
?>
<body style="background-color: #eceef4">
<div class="container" style="position:relative; margin-top:10px">
    <div class="row">
        <?php include '_info.php'; ?>
        <div class="col-lg-6">
            <div class="card shadow bg-white rounded">
                <div class="card-body">
                    <div class="row justify-content">
                        <div class="card border" style="width: 100%;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-titles">Dungeons</h5>
                                <p class="card-text">
                                <div class="mdb-lightbox">
                                    <div class="container-fluid">
                                        <div class="container">
                                            <?php
                                            if (isset($errorMsg)) {
                                                echo '<center><font color="red">' . $errorMsg . '</font></center><br>';
                                            } elseif (isset($infoMsg)) {
                                                echo '<center><font color="green">' . $infoMsg . '</font></center><br>';
                                            }
                                            $column = 0;
                                            foreach (\Game\Game::instance()->wiki->getDungeons() as $dungeon) {
                                                $column++;
                                                if ($column === 1) {
                                                    echo '<div class="row">';
                                                }

                                                include '_dungeon.php';

                                                // If there will be uneven amount of dungeons there will remain an unclosed div<row>
                                                if ($column === 2) {
                                                    echo '</div><hr>';
                                                    $column = 0;
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="d-sm-none">
        </div>
        <?php include '_status.php'; ?>
    </div>
</div>
</body>
<script>
    function refreshDiv() {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById('chat').innerHTML = this.responseText;
            }
        };
        xhttp.open('GET', '?tab=chat', true);
        xhttp.send();
    }

    setInterval(refreshDiv, 3000);

    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>

</html>