<?php

namespace DashboardSimpleLogin;

require_once dirname(__FILE__, 1) . '/Model.php';

use Model;
use FFI\Exception;

// The User controller
class User extends Model
{
  private $sessionToken;

  // Constructor to set DB variables from the client
  function connectToDb(array $db, string $sessionToken)
  {
    // Set the token for creating sessions
    $this->sessionToken = $sessionToken;

    // Set DB credentials
    return $this->setDB($db);
  }

  // Register a user
  public function register(string $username, string $password)
  {
    try {

      // Check if user already exists
      if ($this->getRow($username, 'username', 'dashboard-users')) throw new Exception('User already exists');

      // Encrypt the password
      $passHash = password_hash($password, PASSWORD_DEFAULT);

      // Set the fields and their values
      $time = time();
      $fields = "(`username`, `password`, `dateCreated`)";
      $values = "(?, ?, $time)";

      // Execut the statement
      return $this->setRow([$username, $passHash], "INSERT INTO `dashboard-users` $fields VALUES $values");
    } catch (\Throwable $th) {
      return $th->getMessage();
    }
  }

  // Log the user in 
  public function login(string $username, string $password)
  {
    try {
      // Check if user exists
      $user = $this->getRow($username, 'username', 'dashboard-users');
      if (!$user) throw new Exception('User does\'t exist');

      // Validate password
      $passValid = password_verify($password, $user['password']);
      if (!$passValid) throw new Exception('Password is incorrect');

      // Log the user in with the session variable
      $session = password_hash($user['id'] . $user['password'] . $this->sessionToken, PASSWORD_DEFAULT);
      $_SESSION['LOGIN_SESSION_ID'] = $user['id'];
      $_SESSION['LOGIN_SESSION_TOKEN'] = $session;

      return true;
    } catch (\Throwable $th) {
      return $th->getMessage();
    }
  }

  // Log the user out
  public function logout()
  {
    // Deletes all sessions
    session_destroy();
  }

  // Verify if the user is logged in
  public function isLoggedIn()
  {
    // If variables are not set return false
    if (!isset($_SESSION['LOGIN_SESSION_ID'], $_SESSION['LOGIN_SESSION_TOKEN'])) return false;
    
    // Set the sessions..
    $userId = $_SESSION['LOGIN_SESSION_ID'];
    $session = $_SESSION['LOGIN_SESSION_TOKEN'];

    // Get the user
    $user = $this->getRow($userId, 'id', 'dashboard-users');
    if (!$user) return false;

    // Verify the token and return true or false
    return password_verify($user['id'] . $user['password'] . $this->sessionToken, $session);
  }

  // Change the users password
  public function changePassword()
  {

  }

  // Create the users table
  public function createUsersTable()
  {
    $sql = file_get_contents(dirname(__FILE__, 1) . '/createUsersTable.sql');
    $this->execute($sql);
  }
}
