<?php
/*
---------------------------------  
Connection into MySQl Database
---------------------------------
*/

// Classes Database
class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "pos_system";

        /*
            Function connect MySQL database
        */
    public function connect() {
        try {
            $connection = new PDO(
                "mysql:host={$this->host};dbname={$this->database}",
                $this->username,
                $this->password
            );

            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $connection;

        } catch (PDOException $error) {
            
            die("Connection failed: " . $error->getMessage());
        }
    }
}

