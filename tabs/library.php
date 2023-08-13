<?php include '_header.php'; ?>
<?php
/**
 * @var \Game\Player\Player $player
 * @var \Game\Wiki $wiki
 */
?>
   <body style="background-color: #eceef4">
      <div class="container" style="position:relative; margin-top:10px">
         <div class="row">
             <?php include '_info.php'; ?>
            <div class="col-lg-6">
               <div class="card shadow bg-white rounded">
                  <div class="card-body">
                     <div class="row justify-content">
                     <div class="card-body flex-column">
                        <h5 class="card-titles">Monster library</h5>
                        <div class="container">
                        <div class="row">
                        <input type="text" id="myInput" onkeyup="searchForMonster()" placeholder="Search for monster..">
                        <table id="myTable">
                        <tr class="header">
                            <th style="width:10%;">Name</th>
                            <th style="width:10%;">Health</th>
                            <th style="width:10%;">Experience</th>
                            <th style="width:10%;">Attack</th>
                            <th style="width:10%;">Defence</th>
                        </tr>
                        <?php
                        
                            foreach ($wiki->getMonsters() as $monster)
                            {
                                    echo '<td>';
                                    echo $monster->name;
                                    echo '</td>';
                                    echo '<td>' . $monster->health . '</td>';
                                    echo '<td>' . $monster->exp . '</td>';
                                    echo '<td>' . $monster->attack . '</td>';
                                    echo '<td>' . $monster->defence . '</td>';
                                    echo '</tr>';
                            } 
                        ?>
                        </table>
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
function searchForMonster() {
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
