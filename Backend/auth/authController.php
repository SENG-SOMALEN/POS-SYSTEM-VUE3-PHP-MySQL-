<?php

require_once __DIR__. "/../models/User.php";

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function login(){
        header('Content-Type: application/json');

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Read data from Frontend and convert JSON into Array
        $data = json_decode(
            file_get_contents('php://input'),
            true
        );
        if (!is_array($data)) {
            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Invalid JSON body'
            ]);

            return;
        }

        // Capture value from data
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';
        if (empty($email) || empty($password)) {
            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Email and Password are required'
            ]);

            return;
        }

        // if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        //     http_response_code(400);
        //     echo json_encode([
        //         'success' => false,
        //         'message' => 'Invalid email format'
        //     ]);
        //     return;
        // }

        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Email not found'
            ]);

            return;
        }

        if (!password_verify($password, $user['password_hash'])) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Wrong password'
            ]);

            return;
        }

        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['logged_in']  = true;

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'user'    => [
                'id'    => $user['id'],
                'email' => $user['email'],
                'username'  => $user['username'] ?? null,
            ]
        ]);
    }

    public function logout(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_unset();
        session_destroy();

        echo json_encode([
            'success' => true,
            'message' => 'Logout successful!'
        ]);
    }

    public function register(){
        // Read data from frontend and convert JSON into Array 
        $data = json_decode(
            file_get_contents('php://input'),
            true
        );

        if (!is_array($data)) {
            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Invalid JSON body'
            ]);

            return;
        }

        $required = ['username', 'full_name', 'email', 'password'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                http_response_code(400);

                echo json_encode([
                    'success' => false,
                    'message' => "$field is required"
                ]);

                return;
            }
        }

        $existingUser = $this->userModel->findByEmail($data['email']);
        if ($existingUser) {
            echo json_encode([
                'success' => false,
                'message' => 'Email already exists'
            ]);

            return;
        }

        // convert password simple into hash_password 
        $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['role'] = $data['role'] ?? 'Cashier';

        $result = $this->userModel->create($data);

        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Register successful' : 'Register failed'
        ]);
    }
}
