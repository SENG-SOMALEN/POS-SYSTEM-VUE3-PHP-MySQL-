<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../controller/UserController.php';

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

http_response_code(404);

echo json_encode([
    'success' => false,
    'message' => 'Route not found'
]);