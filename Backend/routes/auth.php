<?php

require_once __DIR__ . "/../auth/AuthController.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $auth = new AuthController();
    $auth->login();
} catch (Throwable $e) {
    http_response_code(500);
    error_log('[AuthRoute] ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Internal server error']);
}