<?php

class Database {
    private $host = 'localhost';
    private $user = 'root';
    private $pass = '';
    private $dbname = 'hollowmountains';
    private $connection;

    // Constructor maakt de verbinding direct
    public function __construct() {
        $this->connection = new mysqli($this->host, $this->user, $this->pass, $this->dbname);

        if ($this->connection->connect_errno) {
            die('Failed to connect to MySQL: ' . $this->connection->connect_error);
        }
    }

    // Haal de actieve verbinding op
    public function getConnection() {
        return $this->connection;
    }

    // Sluit de verbinding
    public function disconnect() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
}
?>
