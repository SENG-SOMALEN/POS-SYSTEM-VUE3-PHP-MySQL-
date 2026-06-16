<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../controller/UserController.php';
require_once __DIR__ . '/../auth/authController.php'; // ធានាថាឈ្មោះ folder 'auth' ត្រូវនឹងក្នុងកុំព្យូទ័រ

$userController = new UserController();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = '/MINI_POS_SYSTEM/Backend/public';
$route = str_replace($basePath, '', $uri);
$segments = explode('/', trim($route, '/'));
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    case 'GET':
        // GET ALL USERS
        if ($route === '/users') {
            $userController->index();
            exit;
        }

        // GET USER BY ID
        if (
            count($segments) === 2 &&
            $segments[0] === 'users' &&
            is_numeric($segments[1])
        ) {
            $id = (int) $segments[1];
            $userController->show($id);
            exit;
        }

        // GET USER BY USERNAME
        if (
            count($segments) === 2 &&
            $segments[0] === 'users'
        ) {
            $username = $segments[1];
            $userController->username($username);
            exit;
        }
        break;

    case 'POST':
        // CREATE USER
        if ($route === '/users') {
            $userController->store();
            exit;
        }

        // 🌟 បន្ថែម ROUTE សម្រាប់ LOGIN នៅត្រង់នេះ
        if ($route === '/login' || $route === '/auth') {
            try {
                $auth = new AuthController();
                $auth->login();
                exit;
            } catch (Throwable $e) {
                http_response_code(500);
                error_log('[AuthRoute] ' . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Internal server error']);
                exit;
            }
        }
        break;

    case 'PUT':
        // UPDATE USER
        if (
            count($segments) === 2 &&
            $segments[0] === 'users' &&
            is_numeric($segments[1])
        ) {
            $id = (int) $segments[1];
            $userController->update($id);
            exit;
        }
        break;

    case 'DELETE':
        // DELETE USER
        if (
            count($segments) === 2 &&
            $segments[0] === 'users' &&
            is_numeric($segments[1])
        ) {
            $id = (int) $segments[1];
            $userController->delete($id);
            exit;
        }
        break;
}

// ករណីរកមិនឃើញ Route ណាដែលត្រូវទាល់តែសោះ (404 Not Found)
http_response_code(404);
echo json_encode([
    'success' => false,
    'message' => 'Route not found'
]);
exit;