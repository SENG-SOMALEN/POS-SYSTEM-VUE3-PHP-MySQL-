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

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid email format'
            ]);
            return;
        }

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
}