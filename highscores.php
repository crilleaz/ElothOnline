<?php
include_once('engine.php');
if (!isset($_SESSION['username'])) {
	header('Location: login.php');
	exit;
}
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
          <a class="dropdown-item" href="#">Highscores</a>
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
                     <div class="card border border-success" style="width: 100%;">
                     <div class="card-body d-flex flex-column">
                        <h5 class="card-titles">Highscores Top 100</h5>
                        <p class="card-text">
                        <div class="mdb-lightbox">
                        <div class="container-fluid">
                        <div class="container">
                        <div class="row">
                        <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for player..">
                        <table id="myTable">
                        <tr class="header">
                            <th style="width:20%;">Name</th>
                            <th style="width:20%;">Level</th>
                            <th style="width:20%;">Experience</th>
                        </tr>
                        <?php
                        
                            $get_highscore = mysqli_query($db,"SELECT name, level, experience FROM players ORDER by level desc LIMIT 100");
                            while($row = mysqli_fetch_array($get_highscore))
                            {
                                if($row['name'] == 'crilleaz' || $row['name'] == 'GM Crille'){
                                    echo '<tr title="' . 'This player is a gamemaster.' . '">';
                                    echo '<td>';
                                    echo '<font color="red">' . $row['name'] . '</font>';
                                    echo '</td>';
                                    echo '<td>' . $row['level'] . '</td>';
                                    echo '<td>' . $row['experience'] . '</td>';
                                    echo '</tr>';
                                }else{
                                    echo '<td>';
                                    echo $row['name'];
                                    echo '</td>';
                                    echo '<td>' . $row['level'] . '</td>';
                                    echo '<td>' . $row['experience'] . '</td>';
                                    echo '</tr>';
                                }

                            } 
                        ?>
                        </table>
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
<script>
function myFunction() {
  // Declare variables
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[0];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}
</script>

</html>