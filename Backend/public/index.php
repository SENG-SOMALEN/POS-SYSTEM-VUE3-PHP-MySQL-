<?php

header('Content-Type: application/json');

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$basePath = '/MINI_POS_SYSTEM/Backend/public';

$route = str_replace($basePath, '', $uri);

require_once __DIR__ . '/../routes/user.php';
require_once __DIR__ . '/../routes/product.php';
require_once __DIR__ . '/../routes/auth.php';

http_response_code(404);

echo json_encode([
    'success' => false,
    'message' => 'Route not found'
]);