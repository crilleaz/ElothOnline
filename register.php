<?php

require_once __DIR__ . '/vendor/autoload.php';

// Check if the registration form has been submitted
if (isset($_POST['register'])) {
  // Get the registration information from the form
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';
  $passwordConfirm = $_POST['password_confirm'] ?? '';

  // Validate the input
  if ($username === '' || $password === '' || $passwordConfirm === '') {
    $error = "All fields are required";
  } else if ($password !== $passwordConfirm) {
    $error = "Passwords do not match";
  } else {
      $result = \Game\Game::instance()->register($username, $password);
      if (!$result instanceof \Game\Error) {
        header("Location: login.php");
        exit;
      } else {
        $error = $result->message;
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
