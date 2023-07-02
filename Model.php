<?php

require_once dirname(__FILE__, 1) . '/Dbh.php';

class Model extends Dbh
{
  // Add a new row
  function setRow(array $values, string $sql)
  {
    $stmt = $this->connect()->prepare($sql);
    try {
      return $stmt->execute($values);
    } catch (\PDOException $th) {
      echo $th->getMessage();
    }
  }

  // Retrieve a row
  function getRow(string $value, string $field, string $table)
  {
    $sql = "SELECT * FROM `$table` WHERE $field = ?";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute([$value]);
    return $stmt->fetch();
  }

  // Execute an SQL statement
  function execute(string $sql)
  {
    try {
      $this->connect()->exec($sql);
    } catch (\PDOException $th) {
      echo $th->getMessage();
    }
  }
}
