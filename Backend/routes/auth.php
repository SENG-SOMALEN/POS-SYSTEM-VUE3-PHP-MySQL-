<?php

require_once __DIR__ . '/../auth/authController.php';

$authController = new AuthController();

$route = Router::getRoute();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {

    if ($route === '/auth/login') {
        $authController->login();
        exit;
    }

    if ($route === '/auth/register') {
        $authController->register();
        exit;
    }

    if ($route === '/auth/logout') {
        $authController->logout();
        exit;
    }
}