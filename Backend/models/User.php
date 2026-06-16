<?php

require_once __DIR__ . "/../config/database.php";

class User {

    private $conn;
    public function __construct(){

        $database = new Database();

        $this->conn = $database->connect();

        if (!$this->conn) {
            throw new \RuntimeException("Database connection failed.");
        }
    }

    /*
    =======================
    | Find user by all
    =======================
    */
    public function findAll(){
        $sqlQuery = "
            SELECT * FROM users ORDER BY id DESC
        ";

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    =======================
    | Find user by id
    =======================
    */
    public function findByID($id){
        $sqlQuery = "
            SELECT * FROM users WHERE id = :id
        ";

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /*
    =======================
    | Find user by username
    =======================
    */
    public function findByUsername($username){
        $sqlQuery = "
        SELECT * FROM users WHERE username = :username
        ";

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute([
            ':username' => $username
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /*
    =======================
    | Create user
    =======================
    */
    public function create($data){
        $createQuery = "
            INSERT INTO users (username, full_name, email, password_hash, role)
            VALUES(:username, :full_name, :email, :password_hash, :role)
        ";

        $stmt = $this->conn->prepare($createQuery);

        return $stmt->execute([
            ':username' => $data['username'],
            ':full_name' => $data['full_name'],
            ':email' => $data['email'],
            ':password_hash' => $data['password_hash'],
            ':role' => $data['role']
        ]);
    }

    /*
    =======================
    | Update user
    =======================
    */
    public function update($id, $data){
        $updateQuery = "
            UPDATE users 
            SET username = :username,
                role = :role
            WHERE id = :id 
        ";

        $stmt = $this->conn->prepare($updateQuery);

        return $stmt->execute([
            ':id' => $id,
            ':username' => $data['username'],
            ':role' => $data['role']
        ]);
    }

    /*
    =======================
    | Delete user
    =======================
    */
    public function delete($id){
        $deleteQuery = "
            DELETE FROM users WHERE id = :id
        ";

        $stmt = $this->conn->prepare($deleteQuery);

        return $stmt->execute([':id' => $id]);
    }

    /*
    =======================
    | Find BY Email
    =======================
    */
    public function findByEmail($email){
        $sqlQuery = "SELECT * FROM users WHERE email = :email LIMIT 1";

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute([
            ':email' => $email
        ]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}