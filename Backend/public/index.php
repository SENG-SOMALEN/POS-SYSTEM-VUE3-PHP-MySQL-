<?php
require_once __DIR__ . '/../models/CartItem.php';

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

} elseif (str_starts_with($route, '/checkout')) {
    require_once __DIR__ . '/../routes/checkout.php';

} elseif (str_starts_with($route, '/inventory')) {
    require_once __DIR__ . '/../routes/inventory.php';

} elseif (str_starts_with($route, '/sales-report')) {
    require_once __DIR__ . '/../routes/sales-report.php';

} else {

    http_response_code(404);

    echo json_encode([
        'success' => false,
        'message' => 'Route not found'
    ]);
}
