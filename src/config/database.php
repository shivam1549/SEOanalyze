<?php
namespace App\Config;

use mysqli;


class Database{
    private $host = 'localhost';
    private $username = 'root';
    private $password = '';
    private $database = 'analyze';
    public $connection;

    public function __construct()
    {
        $this->connection = new mysqli($this->host, $this->username, $this->password, $this->database);
        if($this->connection->connect_error){
            die("Database connection failed: " . $this->connection->connect_error);
        }
    }

    public function getConnection(){
        return $this->connection;
    }

    public function closeConnection(){
        $this->connection->close();
    }
}

?>