<?php

require_once __DIR__ . '/../models/SalesReport.php';

class SalesReportController
{
    private SalesReportModel $model;

    public function __construct()
    {
        $this->model = new SalesReportModel();
    }

    /*
    ==========================================
    ----- Get Sales Summary by Period -----
    ==========================================
    */
    public function getSalesSummaryByPeriod(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            if (!isset($_GET['startDate']) || !isset($_GET['endDate'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Missing required query parameters: startDate, endDate (YYYY-MM-DD)'
                ]);
                return;
            }

            $summary = $this->model->getSalesSummaryByPeriod(
                $_GET['startDate'],
                $_GET['endDate']
            );

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $summary,
                'count' => count($summary)
            ]);

        } catch (Exception $error) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $error->getMessage()
            ]);
        }
    }

    /*
    ==========================================
    ----- Get Overall Sales Summary -----
    ==========================================
    */
    public function getOverallSalesSummary(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            if (!isset($_GET['startDate']) || !isset($_GET['endDate'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Missing required query parameters: startDate, endDate (YYYY-MM-DD)'
                ]);
                return;
            }

            $summary = $this->model->getOverallSalesSummary(
                $_GET['startDate'],
                $_GET['endDate']
            );

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $summary
            ]);

        } catch (Exception $error) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $error->getMessage()
            ]);
        }
    }

    /*
    ==========================================
    ----- Get Sales by Product -----
    ==========================================
    */
    public function getSalesByProduct(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            if (!isset($_GET['startDate']) || !isset($_GET['endDate'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Missing required query parameters: startDate, endDate'
                ]);
                return;
            }

            $limit = (int) ($_GET['limit'] ?? 50);
            $offset = (int) ($_GET['offset'] ?? 0);

            $sales = $this->model->getSalesByProduct(
                $_GET['startDate'],
                $_GET['endDate'],
                $limit,
                $offset
            );

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $sales,
                'count' => count($sales)
            ]);

        } catch (Exception $error) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $error->getMessage()
            ]);
        }
    }

    /*
    ==========================================
    ----- Get Top Selling Products -----
    ==========================================
    */
    public function getTopSellingProducts(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            if (!isset($_GET['startDate']) || !isset($_GET['endDate'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Missing required query parameters: startDate, endDate'
                ]);
                return;
            }

            $limit = (int) ($_GET['limit'] ?? 10);

            $topProducts = $this->model->getTopSellingProducts(
                $_GET['startDate'],
                $_GET['endDate'],
                $limit
            );

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $topProducts,
                'count' => count($topProducts)
            ]);

        } catch (Exception $error) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $error->getMessage()
            ]);
        }
    }

    /*
    ==========================================
    ----- Get Sales by Cashier -----
    ==========================================
    */
    public function getSalesByCashier(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            if (!isset($_GET['startDate']) || !isset($_GET['endDate'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Missing required query parameters: startDate, endDate'
                ]);
                return;
            }

            $sales = $this->model->getSalesByCashier(
                $_GET['startDate'],
                $_GET['endDate']
            );

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $sales,
                'count' => count($sales)
            ]);

        } catch (Exception $error) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $error->getMessage()
            ]);
        }
    }

    /*
    ==========================================
    ----- Get Payment Method Summary -----
    ==========================================
    */
    public function getPaymentMethodSummary(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            if (!isset($_GET['startDate']) || !isset($_GET['endDate'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Missing required query parameters: startDate, endDate'
                ]);
                return;
            }

            $summary = $this->model->getPaymentMethodSummary(
                $_GET['startDate'],
                $_GET['endDate']
            );

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $summary,
                'count' => count($summary)
            ]);

        } catch (Exception $error) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $error->getMessage()
            ]);
        }
    }

    /*
    ==========================================
    ----- Get Hourly Sales Distribution -----
    ==========================================
    */
    public function getHourlySalesDistribution(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            if (!isset($_GET['date'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Missing required query parameter: date (YYYY-MM-DD)'
                ]);
                return;
            }

            $distribution = $this->model->getHourlySalesDistribution($_GET['date']);

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $distribution,
                'count' => count($distribution)
            ]);

        } catch (Exception $error) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $error->getMessage()
            ]);
        }
    }

    /*
    ==========================================
    ----- Get Sales Trend -----
    ==========================================
    */
    public function getSalesTrend(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            if (!isset($_GET['startDate']) || !isset($_GET['endDate'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Missing required query parameters: startDate, endDate'
                ]);
                return;
            }

            $interval = $_GET['interval'] ?? 'day'; // day, week, month

            $trend = $this->model->getSalesTrend(
                $_GET['startDate'],
                $_GET['endDate'],
                $interval
            );

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $trend,
                'count' => count($trend),
                'interval' => $interval
            ]);

        } catch (Exception $error) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $error->getMessage()
            ]);
        }
    }

    /*
    ==========================================
    ----- Get Product Category Sales -----
    ==========================================
    */
    public function getProductCategorySales(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            if (!isset($_GET['startDate']) || !isset($_GET['endDate'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Missing required query parameters: startDate, endDate'
                ]);
                return;
            }

            $sales = $this->model->getProductCategorySales(
                $_GET['startDate'],
                $_GET['endDate']
            );

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $sales,
                'count' => count($sales)
            ]);

        } catch (Exception $error) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $error->getMessage()
            ]);
        }
    }

    /*
    ==========================================
    ----- Get Customer Purchase Behavior -----
    ==========================================
    */
    public function getCustomerBehavior(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            if (!isset($_GET['startDate']) || !isset($_GET['endDate'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Missing required query parameters: startDate, endDate'
                ]);
                return;
            }

            $behavior = $this->model->getCustomerBehavior(
                $_GET['startDate'],
                $_GET['endDate']
            );

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $behavior
            ]);

        } catch (Exception $error) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $error->getMessage()
            ]);
        }
    }

    /*
    ==========================================
    ----- Get Low Revenue Products -----
    ==========================================
    */
    public function getLowRevenueProducts(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            if (!isset($_GET['startDate']) || !isset($_GET['endDate'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Missing required query parameters: startDate, endDate'
                ]);
                return;
            }

            $limit = (int) ($_GET['limit'] ?? 10);

            $products = $this->model->getLowRevenueProducts(
                $_GET['startDate'],
                $_GET['endDate'],
                $limit
            );

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $products,
                'count' => count($products)
            ]);

        } catch (Exception $error) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $error->getMessage()
            ]);
        }
    }

    /*
    ==========================================
    ----- Get Sales Performance Index -----
    ==========================================
    */
    public function getSalesPerformanceIndex(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            $required = ['startDate', 'endDate', 'previousStartDate', 'previousEndDate'];
            foreach ($required as $param) {
                if (!isset($_GET[$param])) {
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'message' => "Missing required query parameter: $param (YYYY-MM-DD)"
                    ]);
                    return;
                }
            }

            $performance = $this->model->getSalesPerformanceIndex(
                $_GET['startDate'],
                $_GET['endDate'],
                $_GET['previousStartDate'],
                $_GET['previousEndDate']
            );

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $performance
            ]);

        } catch (Exception $error) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $error->getMessage()
            ]);
        }
    }
}
