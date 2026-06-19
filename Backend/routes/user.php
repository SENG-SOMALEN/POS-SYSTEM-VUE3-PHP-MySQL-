<?php

require_once __DIR__ . '/../controller/User.php';

$userController = new UserController();

$route = Router::getRoute();

$segments = explode('/', trim($route, '/'));
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {

    if ($route === '/users') {
        $userController->index();
        exit;
    }

    if (
        count($segments) === 2 &&
        $segments[0] === 'users' &&
        is_numeric($segments[1])
    ) {
        $userController->show((int)$segments[1]);
        exit;
    }
}

if ($method === 'POST') {

    if ($route === '/users') {
        $userController->store();
        exit;
    }
}

if ($method === 'PUT') {

    if (
        count($segments) === 2 &&
        $segments[0] === 'users' &&
        is_numeric($segments[1])
    ) {
        $userController->update((int)$segments[1]);
        exit;
    }
}

if ($method === 'DELETE') {

    if (
        count($segments) === 2 &&
        $segments[0] === 'users' &&
        is_numeric($segments[1])
    ) {
        $userController->delete((int)$segments[1]);
        exit;
    }
}