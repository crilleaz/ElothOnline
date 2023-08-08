<?php include '_header.php'; ?>
   <body style="background-color: #eceef4">
      <div class="container" style="position:relative; margin-top:10px">
         <div class="row">
             <?php include '_info.php'; ?>
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
                        
                            foreach (\Game\Game::instance()->listTopPlayers(100) as $topPlayer) {
                                if($topPlayer->isAdmin()){
                                    echo '<tr title="This player is a gamemaster.">';
                                    echo '<td><font color="red">' . $topPlayer->getName() . '</font></td>';
                                }else{
                                    echo '<tr>';
                                    echo '<td>' . $topPlayer->getName() . '</td>';
                                }
                                echo '<td>' . $topPlayer->getLevel() . '</td>';
                                echo '<td>' . $topPlayer->getExp() . '</td>';
                                echo '</tr>';
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
            <?php include '_status.php'; ?>
         </div>
      </div>
   </body>
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