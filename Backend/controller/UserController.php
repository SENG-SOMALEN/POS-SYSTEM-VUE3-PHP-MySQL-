<?php

require_once __DIR__ . "/../models/User.php";

class UserController {
    private $userModel;
    public function __construct(){

        $this->userModel = new User();
    }

    /*
    =======================
    | Get user by all
    =======================
    */
    public function index(){
        $users = $this->userModel->findAll();

        if (!$users) {
            http_response_code(404);

            echo json_encode([
                'success' => false,
                'message' => 'No users found'
            ]);

            return;
        }

        echo json_encode($users);
    }

    /*
    =======================
    | Get user by ID
    =======================
    */
    public function show($id){
        if (!is_numeric($id) || $id <= 0) {
            http_response_code(4000);

            echo json_encode([
                'success' => false,
                'message' => 'Invalid user ID'
            ]);

            return;
        }

        $users = $this->userModel->findByID($id);

        if (!$users) {
            http_response_code(404);

            echo json_encode([
                'success' => false,
                'message' => 'User not found'
            ]);

            return;
        }

        echo json_encode($users);
    }

    /*
    =======================
    | Get user by username
    =======================
    */
    public function username($username){
        if (empty($username)) {
            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Username is required'
            ]);

            return;
        }
        $result = $this->userModel->findByUsername($username);

        echo json_encode([
            'success' => $result
        ]);
    }

    /*
    =======================
    | Create user
    =======================
    */
    public function store(){    
        $data = json_decode(
            file_get_contents("php://input"),
            true
        );  

        if (!$data) {
            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Invalid JSON input'
            ]);

            return;
        }

        // Validate required fields
        if (empty($data['username']) || empty($data['password'])) {
            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Username and password are required'
            ]);

            return;
        }

        // Hash password and remove plain-text password from data
        $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        unset($data['password']);

        $result = $this->userModel->create($data);

        if (!$result) {
            http_response_code(500);

            echo json_encode([
                'success' => false,
                'message' => 'Failed to create user'
            ]);

            return;
        }

        echo json_encode([
            'success' => $result
        ]);
    }

    /*
    =======================
    | Updata user
    =======================
    */
    public function update($id){
        if (!is_numeric($id) || $id <= 0) {
            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Invalid user ID'
            ]);

            return;
        }

        $data = json_decode(
            file_get_contents("php://input"),
            true
        );

        if (!$data) {
            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Invalid JSON input'
            ]);
        }

        if (!empty($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
           
            unset($data['password']);
        }

        $result = $this->userModel->update($id, $data);

        if (!$result) {
            http_response_code(500);
            
            echo json_encode(['success' => false, 'message' => 'Failed to update user']);
            
            return;
        }

        echo json_encode([
            'success' => $result
        ]);
    }

    /*
    =======================
    | Delete user
    =======================
    */
    public function delete($id){
        if (!is_numeric($id) || $id <= 0) {
            http_response_code(400);

            echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
            
            return;
        }

        $result = $this->userModel->delete($id);

        if (!$result) {
            http_response_code(500);
        
            echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
        
            return;
        }

        echo json_encode([
            'success' => $result
        ]);
    }
}