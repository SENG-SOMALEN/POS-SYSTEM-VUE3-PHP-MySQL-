<?php

require_once __DIR__ . '/../controller/SalesReport.php';

$salesReportController = new SalesReportController();

$route = Router::getRoute();
$method = $_SERVER['REQUEST_METHOD'];

/*
=====================================
GET /sales-report/daily-summary
Sales summary by period (daily view)
=====================================
*/
if ($method === 'GET' && $route === '/sales-report/daily-summary') {
    $salesReportController->getSalesSummaryByPeriod();
    exit;
}

/*
=====================================
GET /sales-report/overall-summary
Overall sales summary for period
=====================================
*/
if ($method === 'GET' && $route === '/sales-report/overall-summary') {
    $salesReportController->getOverallSalesSummary();
    exit;
}

/*
=====================================
GET /sales-report/by-product
Sales breakdown by product
=====================================
*/
if ($method === 'GET' && $route === '/sales-report/by-product') {
    $salesReportController->getSalesByProduct();
    exit;
}

/*
=====================================
GET /sales-report/top-products
Top selling products
=====================================
*/
if ($method === 'GET' && $route === '/sales-report/top-products') {
    $salesReportController->getTopSellingProducts();
    exit;
}

/*
=====================================
GET /sales-report/by-cashier
Sales by cashier/user
=====================================
*/
if ($method === 'GET' && $route === '/sales-report/by-cashier') {
    $salesReportController->getSalesByCashier();
    exit;
}

/*
=====================================
GET /sales-report/payment-methods
Payment method breakdown
=====================================
*/
if ($method === 'GET' && $route === '/sales-report/payment-methods') {
    $salesReportController->getPaymentMethodSummary();
    exit;
}

/*
=====================================
GET /sales-report/hourly-distribution
Hourly sales distribution for a day
=====================================
*/
if ($method === 'GET' && $route === '/sales-report/hourly-distribution') {
    $salesReportController->getHourlySalesDistribution();
    exit;
}

/*
=====================================
GET /sales-report/trend
Sales trend over time
=====================================
*/
if ($method === 'GET' && $route === '/sales-report/trend') {
    $salesReportController->getSalesTrend();
    exit;
}

/*
=====================================
GET /sales-report/by-category
Sales by product category
=====================================
*/
if ($method === 'GET' && $route === '/sales-report/by-category') {
    $salesReportController->getProductCategorySales();
    exit;
}

/*
=====================================
GET /sales-report/customer-behavior
Customer purchase behavior analysis
=====================================
*/
if ($method === 'GET' && $route === '/sales-report/customer-behavior') {
    $salesReportController->getCustomerBehavior();
    exit;
}

/*
=====================================
GET /sales-report/low-revenue-products
Low performing products
=====================================
*/
if ($method === 'GET' && $route === '/sales-report/low-revenue-products') {
    $salesReportController->getLowRevenueProducts();
    exit;
}

/*
=====================================
GET /sales-report/performance-index
Sales performance comparison
=====================================
*/
if ($method === 'GET' && $route === '/sales-report/performance-index') {
    $salesReportController->getSalesPerformanceIndex();
    exit;
}

/*
If no route matched, return 404
*/
http_response_code(404);
echo json_encode([
    'success' => false,
    'message' => 'Route not found'
]);
