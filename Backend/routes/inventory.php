<?php

require_once __DIR__ . '/../controller/Inventory.php';

$inventoryController = new InventoryController();

$route = Router::getRoute();
$segments = explode('/', trim($route, '/'));
$method = $_SERVER['REQUEST_METHOD'];

/*
=====================================
GET /inventory
Get all inventory with filters
=====================================
*/
if ($method === 'GET') {

    if ($route === '/inventory') {
        $inventoryController->getAllInventory();
        exit;
    }

    /*
    =====================================
    GET /inventory/summary
    Get inventory summary/overview
    =====================================
    */
    if ($route === '/inventory/summary') {
        $inventoryController->getInventorySummary();
        exit;
    }

    /*
    =====================================
    GET /inventory/valuation
    Stock valuation report
    =====================================
    */
    if ($route === '/inventory/valuation') {
        $inventoryController->getStockValuationReport();
        exit;
    }

    /*
    =====================================
    GET /inventory/low-stock
    Get low stock products
    =====================================
    */
    if ($route === '/inventory/low-stock') {
        $inventoryController->getLowStockProducts();
        exit;
    }

    /*
    =====================================
    GET /inventory/out-of-stock
    Get out of stock products
    =====================================
    */
    if ($route === '/inventory/out-of-stock') {
        $inventoryController->getOutOfStockProducts();
        exit;
    }

    /*
    =====================================
    GET /inventory/slow-moving
    Get slow moving items
    =====================================
    */
    if ($route === '/inventory/slow-moving') {
        $inventoryController->getSlowMovingItems();
        exit;
    }

    /*
    =====================================
    GET /inventory/by-category
    Get inventory grouped by category
    =====================================
    */
    if ($route === '/inventory/by-category') {
        $inventoryController->getInventoryByCategory();
        exit;
    }
}

/*
=====================================
POST /inventory/adjust
Adjust stock quantity
=====================================
*/
if ($method === 'POST') {

    if ($route === '/inventory/adjust') {
        $inventoryController->adjustStock();
        exit;
    }

    /*
    =====================================
    POST /inventory/reconcile
    Perform inventory reconciliation
    =====================================
    */
    if ($route === '/inventory/reconcile') {
        $inventoryController->performReconciliation();
        exit;
    }
}

/*
=====================================
PUT /inventory/alert-quantity
Update alert quantity threshold
=====================================
*/
if ($method === 'PUT') {

    if ($route === '/inventory/alert-quantity') {
        $inventoryController->updateAlertQuantity();
        exit;
    }
}

/*
If no route matched, return 404
*/
http_response_code(404);
echo json_encode([
    'success' => false,
    'message' => 'Route not found'
]);
