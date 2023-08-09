<?php

require_once __DIR__ . '/../bootstrap.php';

// Check if the login form has been submitted
if (isset($_POST['login'])) {
  // Get the login credentials from the form
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';

  session_start();

  $result = \Game\Game::instance()->login($username, $password);

  if ($result instanceof \Game\Engine\Error) {
      $error = $result->message;
  } else {
      header("Location: /");
      exit();
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

<?php
// Check if there is an error message and display it
if (isset($error)) {
    echo "<p style='color:red'>$error</p>";
}
