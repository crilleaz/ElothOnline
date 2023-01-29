<?php

// Connect to the database
$db = mysqli_connect("localhost", "user", "password", "db");

// Check if the registration form has been submitted
if (isset($_POST['register'])) {
  // Get the registration information from the form
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $password = mysqli_real_escape_string($db, $_POST['password']);
  $password_confirm = mysqli_real_escape_string($db, $_POST['password_confirm']);

  // Validate the input
  if (empty($username) || empty($password) || empty($password_confirm)) {
    $error = "All fields are required";
  } else if ($password != $password_confirm) {
    $error = "Passwords do not match";
  } else if (strlen($password) < 8) {
    $error = "Password must be at least 8 characters long";
  } else {
    // Check if the username is already taken
    $query = "SELECT * FROM users WHERE anv = '$username'";
    $result = mysqli_query($db, $query);
    if (mysqli_num_rows($result) > 0) {
      $error = "Username is already taken";
    } else {
      // Generate a random salt
    //   $salt = bin2hex(random_bytes(32));
      // Hash the password with the salt
    //   $password_hash = hash_pbkdf2("sha256", $password, $salt, 100000, 64);
      // Insert the new user into the database
      $ip = $_SERVER['REMOTE_ADDR'];
      $query = "INSERT INTO users (anv, pwd, last_ip) VALUES ('$username', '$password', '$ip')";
      $db->query("INSERT INTO players (id, name, level, experience, stamina, health, health_max, magic, strength, defense, woodcutting, mining, gathering, harvesting, blacksmith, herbalism, gold, crystals, in_combat) VALUES (NULL, '{$username}', '1', '0', '100', '15', '15', '0', '10', '10', '0', '0', '0', '0', '0', '0', '10', '0', '0')");
      $db->query("INSERT INTO chat (id, username, messages, tid) VALUES (NULL, 'System', 'Registration: New member joined!', NULL)");
      $db->query("INSERT INTO inventory (username, item_id, amount, worth) VALUES ('{$username}', '1', '10', '0')");
      $db->query("INSERT INTO log (username, message, tid) VALUES ('{$username}', '[System] Welcome {$username}! <br> This is your Combat log, right now its empty :( <br> Visit <a href=\'dungeons.php\'>Dungeons to start your adventure!</a>', NULL)");
      


      mysqli_query($db, $query);
      // If the insertion was successful, redirect the user to the login page
      if (mysqli_affected_rows($db) > 0) {
        header("Location: login.php");
        exit;
      } else {
        $error = "An error occurred while creating your account. Please try again.";
      }
    }
  }
}

?>

<!-- The registration form -->
<form method="post" action="register.php">
  <label for="username">Username:</label><br>
  <input type="text" name="username" required><br>
  <label for="password">Password:</label><br>
  <input type="password" name="password" required><br>
  <label for="password_confirm">Confirm password:</label><br>
  <input type="password" name="password_confirm" required><br><br>
  <input type="submit" name="register" value="Sign Up">
</form>

<?php

// Check if there is an error message and display it
if (isset($error)) {
  echo "<p style='color:red'>$error</p>";
}

?>
