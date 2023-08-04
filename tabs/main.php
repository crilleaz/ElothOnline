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