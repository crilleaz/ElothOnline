<div class="col-lg-3 d-none d-lg-block">
    <ul class="list-group myStickyListGroup shadow bg-white rounded">
        <li class="list-group-item rounded">
            <center><pre>Status</pre></center>
            <?php
            if($player->isFighting()){
                echo '<font size="2">Combat: <img src="./combat.gif" title="Currently fighting"><br>';
                echo 'Dungeon: ' . getDungeonName($dungeonId = getHuntingDungeonId());
            }elseif($player->isInProtectiveZone()){
                echo '<font size="2">Combat: <img src="./pz.gif" title="In protective zone"><br>';
                echo 'Dungeon: ' . 'In protective zone';
            }else{
                echo '<font size="2">Combat: Status overflow.<br>';
            }
            echo '<br>';

            ?>
            Time left: 20:02
            </font>
        </li>
        <a href="?tab=inventory" class="list-group-item list-group-item-action">Inventory</a>
        <li class="list-group-item rounded">Mining</li>
        <li class="list-group-item rounded">Gathering</li>
        <li class="list-group-item rounded">Harvesting</li>
    </ul>
    <hr class="d-sm-none">
</div>