<?php include '_header.php'; ?>
   <body style="background-color: #eceef4">
      <div class="container" style="position:relative; margin-top:10px">
         <div class="row">
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
              <?php getPlayerLogs($num = 3) ?>
				  </font>
				  </li>
               </ul>
               <hr class="d-sm-none">
            </div>
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
                              if(isset($_GET['error'])) {
                                //your html for error message
                                echo '<center><font color="red">You are already hunting.</font></center>' . '<br>';
                                }elseif(isset($_GET['success'])) {
                                  //your html for error message
                                  echo '<center><font color="green">You started hunting.</font></center>' . '<br>';
                                  }elseif(isset($_GET['left'])) {
                                    //your html for error message
                                    echo '<center><font color="green">You left the dungeon.</font></center>' . '<br>';
                                    }
                                ?>
                            <div class="row" id="dungeon_tier_1">
                            <?php
                                    if(isset($_GET['hunt'])) {
                                        $selectedDungeon = (int)$_GET['hunt'];
                                      // Escape the input to protect against SQL injection attacks
                                      if($selectedDungeon){
                                        // GET är INT
                                        global $db;
                                        $result = mysqli_query($db,"SELECT username FROM hunting WHERE username = '{$player->getName()}'");
                                        if ($result->num_rows === 0) {
                                        //Huntar inte
                                        $db->query("INSERT INTO hunting (username, dungeon_id) VALUES ('{$player->getName()}', '$selectedDungeon')");
                                        $db->query("UPDATE players SET in_combat = 1 WHERE name = '{$player->getName()}'");
                                        echo '<meta http-equiv="Refresh" content="0; url=launcher.php?tab=dungeons&success=hunting">';
                                            
                                        }else {
                                          //Huntar redan
                                          echo '<meta http-equiv="Refresh" content="0; url=launcher.php?tab=dungeons&?error=hunting">';
                                        }
                                      }
                                  }

                                  if(isset($_GET['leave'])) {
                                    // Escape the input to protect against SQL injection attacks
                                    if((int)$_GET['leave']){
                                      // GET är INT
                                      global $db;
                                      $result = mysqli_query($db,"SELECT username FROM hunting WHERE username = '{$player->getName()}'");
                                      if ($result->num_rows === 0) {
                                      //Huntar inte
                                        // Behövs inte tas bort
                                        echo 'huntar inte';
                                      }else {
                                        //Huntar redan
                                          // Tas bort
                                        $db->query("DELETE from hunting WHERE username = '{$player->getName()}'");
                                        $db->query("UPDATE players SET in_combat = 0  WHERE name = '{$player->getName()}'");
                                        echo '<meta http-equiv="Refresh" content="0; url=launcher.php?tab=dungeons&?left=success">';
                                      }
                                    }
                                }

                            $fetch_dungeons = mysqli_query($db,"SELECT * FROM dungeons");
                                while($row = mysqli_fetch_array($fetch_dungeons))
                                {
                                  $dungeonId = $row['id'];
                                  $monster_id = $row['monster_id'];
                                      if($row['difficult'] == '1'){
                                        echo '<div class="col card "><center>' . $row['name'] . '</center>';
                                        echo '<p class="card-text h-100">' . $row['description'] . '</p>';
                                        echo '<x>' . 'Monster: ' . getMonsterName($monsterId = $monster_id) . '<br>';
                                        echo 'Experience: ' . getMonsterExp($monsterId = $monster_id) . ' XP each' . '<br>';
                                        echo 'Difficult: ' . $row['difficult'];
                                        echo '</x><br>';
                                        $result = mysqli_query($db,"SELECT username FROM hunting WHERE username = '{$player->getName()}'");
                                        if ($result->num_rows === 0) {
                                          echo '<a class="btn btn-primary" href="?tab=dungeons&hunt=' . $dungeonId . '"' . 'role="button">Start</a><br>';
                                        }elseif($player->getHuntingDungeonId() == $row['id']){
                                          echo '<a class="btn btn-danger" href="?tab=dungeons&leave=' . $dungeonId . '"' . 'role="button">Leave</a><br>';
                                        }else{
                                          echo '<a class="btn btn-primary" href="?tab=dungeons&hunt=' . $dungeonId . '"' . 'role="button">Start</a><br>';
                                        }
                                        
                                        echo '</div>';
                                }
                              } 
                            echo '</div>';
                            echo '<hr>';
                            echo '<div class="row" id="dungeon_tier_2">';
                            $fetch_dungeons = mysqli_query($db,"SELECT * FROM dungeons");
                                while($row = mysqli_fetch_array($fetch_dungeons))
                                {
                                  $dungeonId = $row['id'];
                                      if($row['difficult'] == '5'){
                                        echo '<div class="col card"><center>' . $row['name'] . '</center>';
                                        echo '<p class="card-text h-100">' . $row['description'] . '</p>';
                                        echo '<x>' . 'Monster: ' . getMonsterName($monsterId = $monster_id) . '<br>';
                                        echo 'Experience: ' . getMonsterExp($monsterId = $monster_id) . ' XP each' . '<br>';
                                        echo 'Difficult: ' . $row['difficult'];
                                        echo '</x><br>';
                                        $result = mysqli_query($db,"SELECT username FROM hunting WHERE username = '{$player->getName()}'");
                                        if ($result->num_rows === 0) {
                                          echo '<a class="btn btn-primary" href="?tab=dungeons&hunt=' . $dungeonId . '"' . 'role="button">Start</a><br>';
                                        }elseif($player->getHuntingDungeonId() == $row['id']){
                                          echo '<a class="btn btn-danger" href="?tab=dungeons&leave=' . $dungeonId . '"' . 'role="button">Leave</a><br>';
                                        }else{
                                          echo '<a class="btn btn-primary" href="?tab=dungeons&hunt=' . $dungeonId . '"' . 'role="button">Start</a><br>';
                                        }
                                        echo '</div>';
                                      }
                                }
                            echo '</div>';
                            echo '<hr>';
                            echo '<div class="row" id="dungeon_tier_3">';
                                $fetch_dungeons = mysqli_query($db,"SELECT * FROM dungeons");
                                    while($row = mysqli_fetch_array($fetch_dungeons))
                                    {
                                      $dungeonId = $row['id'];
                                          if($row['difficult'] == '5'){
                                            echo '<div class="col card"><center>' . $row['name'] . '</center>';
                                            echo '<p class="card-text h-100">' . $row['description'] . '</p>';
                                            echo '<x>' . 'Monster: ' . getMonsterName($monsterId = $monster_id) . '<br>';
                                            echo 'Experience: ' . getMonsterExp($monsterId = $monster_id) . ' XP each' . '<br>';
                                            echo 'Difficult: ' . $row['difficult'];
                                            echo '</x><br>';
                                            $result = mysqli_query($db,"SELECT username FROM hunting WHERE username = '{$player->getName()}'");
                                            if ($result->num_rows === 0) {
                                              echo '<a class="btn btn-primary" href="?tab=dungeons&hunt=' . $dungeonId . '"' . 'role="button">Start</a><br>';
                                            }elseif($player->getHuntingDungeonId() == $row['id']){
                                              echo '<a class="btn btn-danger" href="?tab=dungeons&leave=' . $dungeonId . '"' . 'role="button">Leave</a><br>';
                                            }else{
                                              echo '<a class="btn btn-primary" href="?tab=dungeons&hunt=' . $dungeonId . '"' . 'role="button">Start</a><br>';
                                            }
                                            echo '</div>';
                                          }
                                          // mysqli_close($db);
                                    } 
                              ?>
                            </div>
                        </div>
                        </div>
                        </div>
                        </p>
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
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        document.getElementById('chat').innerHTML = this.responseText;
      }
    };
    xhttp.open('GET', '?tab=chat', true);
    xhttp.send();
  }
  setInterval(refreshDiv, 3000);

  if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>

</html>