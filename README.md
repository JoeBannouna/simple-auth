# Simple Auth - PHP

## Simple & minimalist OOP interface to quickly get started with a login system



### User model functions
```php
<?php

$user = new DashboardSimpleLogin\User();

$user->connectToDb(array $db, string $sessionToken);

$user->register(string $username, string $password);
$user->login(string $username, string $password);
$user->changePassword();  // Does not work yet  (PRs open)

$user->logout();
$user->isLoggedIn();

$user->createUsersTable();
```
Warning: Changing a user's password does not work yet.

### Usage examples 

connect.php
```php
<?php
// Start a session must do for login sessions to work
session_start();

require_once './simple-auth/User.php';

$dbArr = [
  'HOST' => 'localhost',
  'USER' => 'root', 
  'PASS' => '',
  'NAME' => 'simple-auth-db'
];

// Initiated the user object
$user = new DashboardSimpleLogin\User();

// Connected to the database
$connected = $user->connectToDb($dbArr, 'testToken');

// If the user is logged in
$loggedIn = $user->isLoggedIn();

// A function that is used to redirect people to pages
function returnToPage($page) {
  header('Location: ' . $page);
  die;
}
```

index.php
```php
<?php

require_once 'connect.php';

if (!$loggedIn) header('Location: login.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  // Logout
  if ($_POST['action'] == 'logout') {
    $user->logout();
    returnToPage('login.php');
  }

}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
</head>

<body>
  <h1>Dashboard</h1>

  <form action="" method="post">
    <input type="hidden" value="logout" name="action">
    <input type="submit" value="Logout">
  </form>

</body>

</html>
```

register.php
```php
<?php

require_once 'connect.php';

// If the user is logged in redirect to the dashboard
if ($loggedIn) returnToPage('index.php');

// Handle post
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  // If values aint set or empty redirect
  if (
    !isset($_POST['username'], $_POST['password']) &&
    !empty($_POST['username']) &&
    !empty($_POST['password'])
  ) returnToPage('register.php');

  // Login the user and redirect if failed
  if ($user->register($_POST['username'], $_POST['password']) != true) returnToPage('register.php?err');

  returnToPage('index.php');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign up</title>
</head>

<body>
  <h1>Sign up</h1>

  <!-- SIGNUP FORM -->
  <form action="" method="post">
    <input type="text" placeholder="Username" name="username">
    <input type="password" placeholder="Password" name="password">
    <input type="submit" value="Sign up">
  </form>

</body>

</html>
```

login.php
```php
<?php

require_once 'connect.php';

// If the user is logged in redirect to the dashboard
if ($loggedIn) returnToPage('index.php');

// Handle post
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  // Validate the username and password
  if (
    !isset($_POST['username'], $_POST['password']) ||
    empty($_POST['username']) ||
    empty($_POST['password'])
  ) returnToPage('login.php?empty');

  // Login the user
  $login = $user->login($_POST['username'], $_POST['password']);
  // If failed redirect back to login
  if ($login != true) returnToPage('login.php?err');
  
  // If success go to home page
  returnToPage('index.php');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Log in</title>
</head>

<body>
  <h1>Log in</h1>

  <!-- LOGIN FORM -->
  <form action="" method="post">
    <input type="text" placeholder="Username" name="username">
    <input type="password" placeholder="Password" name="password">
    <input type="submit" value="Login">
  </form>

</body>

</html>
```
