<?php

class Dbh
{
    private $host;
    private $user;
    private $pwd;
    private $dbName;

    protected function setDB(array $DB)
    {
        $this->host = $DB["HOST"];
        $this->user = $DB["USER"];
        $this->pwd = $DB["PASS"];
        $this->dbName = $DB["NAME"];

        return $this->connect() ? true : false;
    }

    protected function connect()
    {
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbName;
        $pdo = new PDO($dsn, $this->user, $this->pwd);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->setAttribute(PDO::ERRMODE_EXCEPTION, true);
        return $pdo;
    }
}
