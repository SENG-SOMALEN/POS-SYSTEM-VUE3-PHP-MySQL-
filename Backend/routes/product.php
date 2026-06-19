<?php

require_once __DIR__ . '/../controller/Product.php';

$productController = new ProductController();

$route = Router::getRoute();

$segments = explode('/', trim($route, '/'));
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {

    if ($route === '/products') {
        $productController->getProductAll();
        exit;
    }

    if (
        count($segments) === 2 &&
        $segments[0] === 'products' &&
        is_numeric($segments[1])
    ) {
        $productController->getIDproduct((int)$segments[1]);
        exit;
    }
}

if ($method === 'POST') {

    if ($route === '/products') {
        $productController->postProduct();
        exit;
    }
}

if ($method === 'PUT') {

    if (
        count($segments) === 2 &&
        $segments[0] === 'products' &&
        is_numeric($segments[1])
    ) {
        $productController->putProduct((int)$segments[1]);
        exit;
    }
}

if ($method === 'DELETE') {

    if (
        count($segments) === 2 &&
        $segments[0] === 'products' &&
        is_numeric($segments[1])
    ) {
        $productController->deleteProduct((int)$segments[1]);
        exit;
    }
}