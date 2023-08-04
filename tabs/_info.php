<div class="col-lg-3 d-none d-lg-block">
    <ul class="list-group myStickyListGroup shadow bg-white rounded">
        <li class="list-group-item rounded">
            <font size="5"><b><?php echo $_SESSION['username']; ?></b></font> Lv. <?php print_r($player->getLevel()) ?><br>
            <font size="2">HP: <?php print_r($player->getCurrentHealth()) ?>/<?php print_r($player->getMaxHealth()) ?></font>
            |
            <?php
            if($player->getStamina() < 50){
                echo '<font size="2">Stamina: ' .'<font color="yellow">' . $player->getStamina() . '</font>' . '/100</font>';
            }elseif($player->getStamina() < 10){
                echo '<font size="2">Stamina: ' .'<font color="red">' . $player->getStamina() . '</font>' . '/100</font>';
            }else{
                echo '<font size="2">Stamina: ' . $player->getStamina() . '/100</font>';
            }
            ?>
            <hr>
            <center><pre>Skills</pre></center>
            <font size="2">Experience: <?php print_r($player->getExp()) ?> <?php echo " / " ?> <?php print_r($player->getNextLevelExp())?><br>
                Magic: <?php print_r($player->getMagic()) ?> <br>
                Strength: <?php print_r($player->getStrength()) ?> <br>
                Defense: <?php print_r($player->getDefense()) ?> <br>

            </font>
        </li>
        <li class="list-group-item rounded">
            <center><pre>Abilities</pre></center>
            <font size="2">
                Woodcutting: <?php print_r($player->getWoodcutting()) ?><br>
                Mining: <?php print_r($player->getMining()) ?> <br>
                Gathering: <?php print_r($player->getGathering()) ?> <br>
                Harvesting: <?php print_r($player->getHarvesting()) ?> <br>
                Blacksmith: <?php print_r($player->getBlacksmith()) ?> <br>
                Herbalism: <?php print_r($player->getHerbalism()) ?> <br>
            </font>
        </li>
        <li class="list-group-item rounded">
            <center><pre>Bank</pre></center>
            <font size="2">
                Gold: <?php print_r($player->getGold()) ?><br>
                Crystals: <?php print_r($player->getCrystals()) ?> <br>
            </font>
        </li>
        <li class="list-group-item rounded">
            <center><pre>Log</pre></center>
            <font size="1">
                <?php
                foreach ($player->getLogs(3) as $log) {
                    echo $log . '<br>';
                }
                ?>
            </font>
        </li>
    </ul>
    <hr class="d-sm-none">
</div>