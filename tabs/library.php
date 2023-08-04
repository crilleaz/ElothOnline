<?php include '_header.php'; ?>
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
                        <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for monster..">
                        <table id="myTable">
                        <tr class="header">
                            <th style="width:10%;">Name</th>
                            <th style="width:10%;">Health</th>
                            <th style="width:10%;">Experience</th>
                            <th style="width:10%;">Attack</th>
                            <th style="width:10%;">Defense</th>
                        </tr>
                        <?php
                        
                            $get_highscore = mysqli_query($db,"SELECT name, health, experience, attack, defense FROM monster");
                            while($row = mysqli_fetch_array($get_highscore))
                            {
                                    echo '<td>';
                                    echo $row['name'];
                                    echo '</td>';
                                    echo '<td>' . $row['health'] . '</td>';
                                    echo '<td>' . $row['experience'] . '</td>';
                                    echo '<td>' . $row['attack'] . '</td>';
                                    echo '<td>' . $row['defense'] . '</td>';
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