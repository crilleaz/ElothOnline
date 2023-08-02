<?php
include_once('engine.php');
if (!isset($_SESSION['username'])) {
	header('Location: login.php');
	exit;
}

// error_reporting(E_ALL);
// ini_set("display_errors", 1);
?>

<!DOCTYPE html>
<html lang="en">
   <head>
      <title>Eloth Online</title>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
      <link rel="stylesheet" href="style.css">
   </head>
   <nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="#">GAME</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="./">Home <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="dungeons.php">Dungeons</a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Community
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="#">News</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="#">Arena</a>
          <a class="dropdown-item" href="#">Guilds</a>
          <a class="dropdown-item" href="library.php">Library</a>
          <a class="dropdown-item" href="highscores.php">Highscores</a>
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          World
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="#">Woodcutting</a>
          <a class="dropdown-item" href="#">Mining</a>
          <a class="dropdown-item" href="#">Gathering</a>
          <a class="dropdown-item" href="#">Harvesting</a>
          <a class="dropdown-item" href="#">Blacksmith</a>
          <a class="dropdown-item" href="#">Herbalism</a>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="logout.php">Logout</a>
      </li>
    </ul>
  </div>
</nav>


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
                                      // Escape the input to protect against SQL injection attacks
                                      if((int)$_GET['hunt']){
                                        // GET är INT
                                        $get_id_from_get = getHuntingDungeonForStartHunt($getId = $_GET['hunt']);
                                        global $db;
                                        $result = mysqli_query($db,"SELECT username FROM hunting WHERE username = '{$username}'");
                                        if ($result->num_rows === 0) {
                                        //Huntar inte
                                        $db->query("INSERT INTO hunting (username, dungeon_id) VALUES ('{$username}', '$get_id_from_get')");
                                        $db->query("UPDATE players SET in_combat = 1 WHERE name = '{$username}'");
                                        echo '<meta http-equiv="Refresh" content="0; url=dungeons.php?success=hunting">';
                                            
                                        }else {
                                          //Huntar redan
                                          echo '<meta http-equiv="Refresh" content="0; url=dungeons.php?error=hunting">';
                                        }
                                      }
                                  }

                                  if(isset($_GET['leave'])) {
                                    // Escape the input to protect against SQL injection attacks
                                    if((int)$_GET['leave']){
                                      // GET är INT
                                      global $db;
                                      $result = mysqli_query($db,"SELECT username FROM hunting WHERE username = '{$username}'");
                                      if ($result->num_rows === 0) {
                                      //Huntar inte
                                        // Behövs inte tas bort
                                        echo 'huntar inte';
                                      }else {
                                        //Huntar redan
                                          // Tas bort
                                        $db->query("DELETE from hunting WHERE username = '{$username}'");
                                        $db->query("UPDATE players SET in_combat = 0  WHERE name = '{$username}'");
                                        echo '<meta http-equiv="Refresh" content="0; url=dungeons.php?left=success">';
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
                                        $result = mysqli_query($db,"SELECT username FROM hunting WHERE username = '{$username}'");
                                        if ($result->num_rows === 0) {
                                          echo '<a class="btn btn-primary" href="?hunt=' . $dungeonId . '"' . 'role="button">Start</a><br>';
                                        }elseif(getHuntingDungeonId() == $row['id']){
                                          echo '<a class="btn btn-danger" href="?leave=' . $dungeonId . '"' . 'role="button">Leave</a><br>';
                                        }else{
                                          echo '<a class="btn btn-primary" href="?hunt=' . $dungeonId . '"' . 'role="button">Start</a><br>';
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
                                        $result = mysqli_query($db,"SELECT username FROM hunting WHERE username = '{$username}'");
                                        if ($result->num_rows === 0) {
                                          echo '<a class="btn btn-primary" href="?hunt=' . $dungeonId . '"' . 'role="button">Start</a><br>';
                                        }elseif(getHuntingDungeonId() == $row['id']){
                                          echo '<a class="btn btn-danger" href="?leave=' . $dungeonId . '"' . 'role="button">Leave</a><br>';
                                        }else{
                                          echo '<a class="btn btn-primary" href="?hunt=' . $dungeonId . '"' . 'role="button">Start</a><br>';
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
                                            $result = mysqli_query($db,"SELECT username FROM hunting WHERE username = '{$username}'");
                                            if ($result->num_rows === 0) {
                                              echo '<a class="btn btn-primary" href="?hunt=' . $dungeonId . '"' . 'role="button">Start</a><br>';
                                            }elseif(getHuntingDungeonId() == $row['id']){
                                              echo '<a class="btn btn-danger" href="?leave=' . $dungeonId . '"' . 'role="button">Leave</a><br>';
                                            }else{
                                              echo '<a class="btn btn-primary" href="?hunt=' . $dungeonId . '"' . 'role="button">Start</a><br>';
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
                  <a href="inventory.php" class="list-group-item list-group-item-action">Inventory</a>
                  <li class="list-group-item rounded">Mining</li>
                  <li class="list-group-item rounded">Gathering</li>
                  <li class="list-group-item rounded">Harvesting</li>
               </ul>
               <hr class="d-sm-none">
            </div>
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
    xhttp.open('GET', 'chat.php', true);
    xhttp.send();
  }
  setInterval(refreshDiv, 3000);

  if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>

</html>