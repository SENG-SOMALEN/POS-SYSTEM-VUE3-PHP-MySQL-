<?php

require_once __DIR__ . '/../controller/Cart.php';

$cartController = new CartController();

$route = Router::getRoute();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    case 'GET':

        if ($route === '/cart') {
            $cartController->getCart();
        }

        break;

    case 'POST':

        if ($route === '/cart/add') {
            $cartController->addItem();
        }

        break;

    case 'PUT':

        if ($route === '/cart/update') {
            $cartController->updateQuantity();
        }

        break;

    case 'DELETE':

        if ($route === '/cart/remove') {
            $cartController->removeItem();
        }

        elseif ($route === '/cart/clear') {
            $cartController->clearCart();
        }

        break;

    default:

        http_response_code(405);

        echo json_encode([
            'success' => false,
            'message' => 'Method Not Allowed'
        ]);
}