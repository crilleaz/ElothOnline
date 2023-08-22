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
                        <h5 class="card-titles">Inventory</h5>
                        <p class="card-text">
                        <div class="mdb-lightbox">
                        <div class="container-fluid">
                        <div class="container">
                        <div class="row">
                        <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for item..">
                        <table id="myTable">
                        <tr class="header">
                            <th style="width:20%;">Name</th>
                            <th style="width:20%;">Amount</th>
                            <th style="width:20%;">Action</th>
                        </tr>
                        <?php
                        foreach ($player->getInventory() as $item) {
                            if ($item->isSellable) {
                                echo '<tr title="' . 'Worth ' . $item->worth . ' GP each' . '">';
                            } else {
                                echo '<tr title="' . 'Cannot be sold' . '">';
                            }

                            echo '<td>';
                            echo $item->name;
                            echo '</td>';
                            echo '<td>' . $item->quantity . '</td>';
                            if ($item->isSellable) {
                                echo '<td><button type="button" class="btn btn-success">Sell</button> <button type="button" class="btn btn-success">Use</button></td>';
                            }
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
