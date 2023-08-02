<?php

// Connect to the database
$db = include 'db.php';

// Check if the login form has been submitted
if (isset($_POST['login'])) {
  // Get the login credentials from the form
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $password = mysqli_real_escape_string($db, $_POST['password']);

  // Check if the username and password are correct
  $query = "SELECT * FROM users WHERE anv = '$username' AND pwd = '$password'";
  $result = mysqli_query($db, $query);
  $row  = mysqli_fetch_array($result);
  if (mysqli_num_rows($result) == 1) {
    // Login is successful
    // Start a session and set a session variable to indicate that the user is logged in
    session_start();
    $_SESSION['username'] = $row['anv'];
    // Redirect the user to the dashboard
    header("Location: index.php");
    exit;
  } else {
    // Login is unsuccessful
    // Show an error message
    $error = "Invalid username or password";
  }
}

?>

<!-- The login form -->
<form method="post" action="login.php">
  <label for="username">Username:</label><br>
  <input type="text" name="username" required><br>
  <label for="password">Password:</label><br>
  <input type="password" name="password" required><br><br>
  <input type="submit" name="login" value="Log In">
</form>
<a href="register.php">Register</a>