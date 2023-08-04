<?php
/**
 * @var \Game\Dungeon $dungeon
 * @var \Game\Player $player
 */
?>
<div class="col card ">
    <center><?=$dungeon->name?></center>
    <p class="card-text h-100"><?=$dungeon->description?></p>
    <x>Monster: <?=$dungeon->inhabitant->name?><br>
        Experience: <?=$dungeon->inhabitant->exp?> XP each<br>
        Difficult: <?=$dungeon->difficulty?>
    </x><br>
    <?php if ($player->isInDungeon($dungeon)) {
        echo '<a class="btn btn-danger" href="?tab=dungeons&leave=' . $dungeon->id . '"' . 'role="button">Leave</a><br>';
    } else {
        echo '<a class="btn btn-primary" href="?tab=dungeons&hunt=' . $dungeon->id . '"' . 'role="button">Start</a><br>';
    }
    ?>

</div>