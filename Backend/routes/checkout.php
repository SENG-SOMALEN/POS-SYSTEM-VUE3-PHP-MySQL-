<?php

require_once __DIR__ . '/../controller/Checkout.php';

$checkoutController = new CheckoutController();

$route = Router::getRoute();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {

    if ($route === '/checkout') {

        $checkoutController->checkout();
        exit;
    }
}