<?php
session_start();

header('Content-Type: application/json');

require_once __DIR__ . '/../core/Route.php';

$route = Router::getRoute();

if (str_starts_with($route, '/auth')) {

    require_once __DIR__ . '/../routes/auth.php';

} elseif (str_starts_with($route, '/user')) {

    require_once __DIR__ . '/../routes/user.php';

} elseif (str_starts_with($route, '/product')) {

    require_once __DIR__ . '/../routes/product.php';

} elseif (str_starts_with($route, '/cart')) {

    require_once __DIR__ . '/../routes/cart.php';

} else {

    http_response_code(404);

    echo json_encode([
        'success' => false,
        'message' => 'Route not found'
    ]);
}