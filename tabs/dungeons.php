<?php include '_header.php'; ?>
<?php

/**
 * @var \Game\Player\Player $player
 */

$wiki = DI::getService(\Game\Wiki::class);

if (isset($_GET['hunt'])) {
    $selectedDungeon = (int)$_GET['hunt'];

    $result = $player->enterDungeon($selectedDungeon);
    if ($result instanceof \Game\Engine\Error) {
        echo '<meta http-equiv="Refresh" content="0; url=?tab=dungeons&error='.$result->message.'">';
    } else {
        echo '<meta http-equiv="Refresh" content="0; url=?tab=dungeons&success=hunting">';
    }
}

if (isset($_GET['leave'])) {
    $player->leaveDungeon();
    echo '<meta http-equiv="Refresh" content="0; url=launcher.php?tab=dungeons&left=success">';
}

if (isset($_GET['error']) && is_string($_GET['error'])) {
    //your html for error message
    $errorMsg = htmlspecialchars($_GET['error']);
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
                                            foreach ($wiki->getDungeons() as $dungeon) {
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

</html>
