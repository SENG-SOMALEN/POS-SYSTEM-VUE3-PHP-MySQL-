<?php

require_once __DIR__ . '/../models/Inventory.php';

class InventoryController
{
    private InventoryModel $model;

    public function __construct()
    {
        $this->model = new InventoryModel();
    }

    /*
    ============================================
    ----- Get All Inventory with Filters -----
    ============================================
    */
    public function getAllInventory(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            $searchTerm = $_GET['search'] ?? null;
            $sortBy = $_GET['sortBy'] ?? 'product_name';
            $sortOrder = $_GET['sortOrder'] ?? 'ASC';
            $limit = (int) ($_GET['limit'] ?? 50);
            $offset = (int) ($_GET['offset'] ?? 0);

            $inventory = $this->model->getAllInventory(
                $searchTerm,
                $sortBy,
                $sortOrder,
                $limit,
                $offset
            );

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $inventory,
                'count' => count($inventory)
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
    ======================================
    ----- Get Inventory Summary -----
    ======================================
    */
    public function getInventorySummary(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            $summary = $this->model->getInventorySummary();

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
    ====================================
    ----- Adjust Stock Quantity -----
    ====================================
    */
    public function adjustStock(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            $data = json_decode(file_get_contents('php://input'), true);

            // Validate required fields
            $required = ['productID', 'adjustmentQuantity', 'reason', 'userID'];
            foreach ($required as $field) {
                if (!isset($data[$field])) {
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'message' => "Missing required field: $field"
                    ]);
                    return;
                }
            }

            $result = $this->model->adjustStock(
                (int) $data['productID'],
                (int) $data['adjustmentQuantity'],
                $data['reason'],
                (int) $data['userID'],
                $data['notes'] ?? null
            );

            http_response_code(200);
            echo json_encode([
                'success' => $result,
                'message' => 'Stock adjusted successfully'
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
    ========================================
    ----- Update Alert Quantity -----
    ========================================
    */
    public function updateAlertQuantity(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (!isset($data['productID']) || !isset($data['alertQuantity'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Missing required fields: productID, alertQuantity'
                ]);
                return;
            }

            $result = $this->model->updateAlertQuantity(
                (int) $data['productID'],
                (int) $data['alertQuantity']
            );

            http_response_code(200);
            echo json_encode([
                'success' => $result,
                'message' => 'Alert quantity updated successfully'
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
    ================================================
    ----- Stock Valuation Report -----
    ================================================
    */
    public function getStockValuationReport(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            $report = $this->model->getStockValuationReport();

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $report,
                'count' => count($report)
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
    ----- Inventory Reconciliation -----
    ==========================================
    */
    public function performReconciliation(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            $data = json_decode(file_get_contents('php://input'), true);

            // Validate required fields
            $required = ['productID', 'actualQuantity', 'userID'];
            foreach ($required as $field) {
                if (!isset($data[$field])) {
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'message' => "Missing required field: $field"
                    ]);
                    return;
                }
            }

            $result = $this->model->performInventoryReconciliation(
                (int) $data['productID'],
                (int) $data['actualQuantity'],
                (int) $data['userID'],
                $data['notes'] ?? null
            );

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Inventory reconciliation completed',
                'data' => $result
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
    ----- Get Low Stock Products -----
    ==========================================
    */
    public function getLowStockProducts(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            $products = $this->model->getLowStockProducts();

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
    ----- Get Out of Stock Products -----
    ==========================================
    */
    public function getOutOfStockProducts(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            $products = $this->model->getOutOfStockProducts();

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
    ----- Get Slow Moving Items -----
    ==========================================
    */
    public function getSlowMovingItems(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            $daysThreshold = (int) ($_GET['days'] ?? 30);
            $items = $this->model->getSlowMovingItems($daysThreshold);

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $items,
                'count' => count($items),
                'daysThreshold' => $daysThreshold
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
    ----- Get Inventory by Category -----
    ==========================================
    */
    public function getInventoryByCategory(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            $byCategory = $this->model->getInventoryByCategory();

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $byCategory,
                'count' => count($byCategory)
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
