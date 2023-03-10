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
				  <font size="5"><b><?php echo $_SESSION['username']; ?></b></font> Lv. <?php print_r(getPlayerLevel()) ?><br>
				  <font size="2">HP: <?php print_r(getPlayerHealth()) ?>/<?php print_r(getPlayerHealthMax()) ?></font>
				  |
              <?php
              $stamina = getPlayerStamina();

              if ($stamina <= 10) {
               echo '<font size="2">Stamina: ' .'<font color="red">' . getPlayerStamina() . '</font>' . '/100</font>';
             } else{
               echo '<font size="2">Stamina: ' . getPlayerStamina() . '/100</font>';
             }
				  ?>
				  <hr>
				  <center><pre>Skills</pre></center>
				  <font size="2">Experience: <?php print_r(getPlayerExp()) ?> <?php echo " / " ?> <?php print_r(getPlayerNeedExp())?><br>
				  Magic: <?php print_r(getPlayerMagic()) ?> <br>
				  Strength: <?php print_r(getPlayerStrength()) ?> <br>
				  Defense: <?php print_r(getPlayerDefense()) ?> <br>
				  
				  </font>
				  </li>
                  <li class="list-group-item rounded">
				  <center><pre>Abilities</pre></center>
				  <font size="2">
				  Woodcutting: <?php print_r(getPlayerWoodcutting()) ?><br>
				  Mining: <?php print_r(getPlayerMining()) ?> <br>
				  Gathering: <?php print_r(getPlayerGathering()) ?> <br>
				  Harvesting: <?php print_r(getPlayerHarvesting()) ?> <br>
				  Blacksmith: <?php print_r(getPlayerBlacksmith()) ?> <br>
				  Herbalism: <?php print_r(getPlayerHerbalism()) ?> <br>
				  </font>
				  </li>
                  <li class="list-group-item rounded">
				  <center><pre>Bank</pre></center>
				  <font size="2">
				  Gold: <?php print_r(getPlayerGold()) ?><br>
				  Crystals: <?php print_r(getPlayerCrystals()) ?> <br>
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
                     <div class="card-body">
                        <h5 class="card-title">Combatlog</h5>
                        <p class="card-text">
                        <div class="mdb-lightbox">
                        <div id="combatlog">
                        Loading combatlog..
                        </div>
                        </div>
                        </p>
                     </div>
                     </div>
                     </div>
                  </div>
               </div>
               <div class="card shadow bg-white rounded">
                  <div class="card-body">
                     <div class="row justify-content">
                     <div class="card border" style="width: 100%;">
                     <div class="card-body">
                        <h5 class="card-title">Chat</h5>
                        <p class="card-text">
                        <div class="mdb-lightbox">
                        <div id="chat">
                        <?php include("chat.php"); ?>
                     </div>
                     <form method="post" action=""><br>
                     <input type="text" name="message" placeholder="Write something.." autofocus><br>
                     </form>
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
                  if(getPlayerStatus() == '1'){
                     echo '<font size="2">Combat: <img src="./combat.gif" title="Currently fighting"><br>';
                     echo 'Dungeon: ' . getDungeonName($dungeonId = getHuntingDungeonId());
                  }elseif(getPlayerStatus() == '0'){
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
  function refreshDiv() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        document.getElementById('combatlog').innerHTML = this.responseText;
      }
    };
    xhttp.open('GET', 'log.php', true);
    xhttp.send();
  }
  setInterval(refreshDiv, 3000);

  if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>

</html>