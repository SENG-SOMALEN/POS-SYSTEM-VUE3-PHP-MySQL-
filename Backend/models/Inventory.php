<?php

require_once __DIR__ . '/../config/database.php';

class InventoryModel
{
    private PDO $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();

        if (!$this->conn) {
            throw new Exception('Database connection failed.');
        }
    }

    /*
    =============================================
    ----- Get All Inventory with Filtering -----
    =============================================
    */
    public function getAllInventory(
        ?string $searchTerm = null,
        ?string $sortBy = 'product_name',
        string $sortOrder = 'ASC',
        int $limit = 50,
        int $offset = 0
    ): array {
        $sql = "
            SELECT
                p.id,
                p.barcode,
                p.product_name,
                p.cost_price,
                p.retail_price,
                p.stock_quantity,
                p.alert_quantity,
                p.unit,
                (p.cost_price * p.stock_quantity) as stock_value,
                CASE
                    WHEN p.stock_quantity <= p.alert_quantity THEN 'LOW'
                    WHEN p.stock_quantity = 0 THEN 'OUT_OF_STOCK'
                    ELSE 'OK'
                END as stock_status,
                (p.retail_price - p.cost_price) as profit_margin,
                COUNT(sm.id) as movement_count
            FROM products p
            LEFT JOIN stock_movements sm ON p.id = sm.product_id
            WHERE 1=1
        ";

        $params = [];

        // Search filter
        if ($searchTerm !== null && $searchTerm !== '') {
            $sql .= " AND (
                p.product_name LIKE :searchTerm
                OR p.barcode LIKE :searchTerm
                OR p.unit LIKE :searchTerm
            )";
            $params[':searchTerm'] = '%' . $searchTerm . '%';
        }

        // Valid sort columns
        $validSortColumns = ['product_name', 'stock_quantity', 'stock_value', 'stock_status', 'alert_quantity'];
        if (!in_array($sortBy, $validSortColumns)) {
            $sortBy = 'product_name';
        }

        // Validate sort order
        $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';

        $sql .= " GROUP BY p.id, p.barcode, p.product_name, p.cost_price, p.retail_price, 
                  p.stock_quantity, p.alert_quantity, p.unit";
        $sql .= " ORDER BY $sortBy $sortOrder";
        $sql .= " LIMIT :limit OFFSET :offset";

        $params[':limit'] = $limit;
        $params[':offset'] = $offset;

        try {
            $stmt = $this->conn->prepare($sql);

            foreach ($params as $key => $value) {
                if ($key === ':limit' || $key === ':offset') {
                    $stmt->bindValue($key, $value, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue($key, $value);
                }
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $error) {
            throw new Exception('Failed to get inventory: ' . $error->getMessage());
        }
    }

    /*
    ====================================
    ----- Get Inventory Summary -----
    ====================================
    */
    public function getInventorySummary(): array
    {
        $sql = "
            SELECT
                COUNT(id) as total_products,
                SUM(stock_quantity) as total_units,
                SUM(cost_price * stock_quantity) as total_cost_value,
                SUM(retail_price * stock_quantity) as total_retail_value,
                SUM(retail_price * stock_quantity) - SUM(cost_price * stock_quantity) as total_profit_potential,
                SUM(CASE WHEN stock_quantity = 0 THEN 1 ELSE 0 END) as out_of_stock_count,
                SUM(CASE WHEN stock_quantity <= alert_quantity THEN 1 ELSE 0 END) as low_stock_count,
                AVG(stock_quantity) as avg_stock_per_product
            FROM products
        ";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (Exception $error) {
            throw new Exception('Failed to get inventory summary: ' . $error->getMessage());
        }
    }

    /*
    ========================================
    ----- Adjust Stock Quantity -----
    ========================================
    */
    public function adjustStock(
        int $productID,
        int $adjustmentQuantity,
        string $reason,
        int $userID,
        ?string $notes = null
    ): bool {
        try {
            // Start transaction
            $this->conn->beginTransaction();

            try {
                // Verify product exists
                $checkSQL = "SELECT stock_quantity FROM products WHERE id = :productID";
                $stmt = $this->conn->prepare($checkSQL);
                $stmt->execute([':productID' => $productID]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$product) {
                    throw new Exception('Product not found.');
                }

                // Check if stock would go negative
                $newStock = $product['stock_quantity'] + $adjustmentQuantity;
                if ($newStock < 0) {
                    throw new Exception('Adjustment would result in negative stock.');
                }

                // Update product stock
                $updateSQL = "
                    UPDATE products
                    SET stock_quantity = stock_quantity + :adjustment
                    WHERE id = :productID
                ";

                $stmt = $this->conn->prepare($updateSQL);
                $stmt->execute([
                    ':adjustment' => $adjustmentQuantity,
                    ':productID' => $productID
                ]);

                // Record movement in stock_movements
                $movementType = $adjustmentQuantity > 0 ? 'IN' : 'OUT';
                $quantity = abs($adjustmentQuantity);

                $movementSQL = "
                    INSERT INTO stock_movements
                    (product_id, movement_type, quantity, unit_cost, reason, notes, user_id, created_at)
                    VALUES
                    (:productID, :movementType, :quantity, 0, :reason, :notes, :userID, NOW())
                ";

                $stmt = $this->conn->prepare($movementSQL);
                $stmt->execute([
                    ':productID' => $productID,
                    ':movementType' => $movementType,
                    ':quantity' => $quantity,
                    ':reason' => $reason,
                    ':notes' => $notes,
                    ':userID' => $userID
                ]);

                // Commit transaction
                $this->conn->commit();

                return true;

            } catch (Exception $error) {
                $this->conn->rollBack();
                throw $error;
            }

        } catch (Exception $error) {
            throw new Exception('Failed to adjust stock: ' . $error->getMessage());
        }
    }

    /*
    ==========================================
    ----- Update Alert Quantity -----
    ==========================================
    */
    public function updateAlertQuantity(
        int $productID,
        int $alertQuantity
    ): bool {
        if ($alertQuantity < 0) {
            throw new InvalidArgumentException('Alert quantity cannot be negative.');
        }

        try {
            $sql = "
                UPDATE products
                SET alert_quantity = :alertQuantity
                WHERE id = :productID
            ";

            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([
                ':alertQuantity' => $alertQuantity,
                ':productID' => $productID
            ]);

            if ($stmt->rowCount() === 0) {
                throw new Exception('Product not found.');
            }

            return $result;

        } catch (Exception $error) {
            throw new Exception('Failed to update alert quantity: ' . $error->getMessage());
        }
    }

    /*
    ========================================
    ----- Stock Valuation Report -----
    ========================================
    */
    public function getStockValuationReport(): array
    {
        $sql = "
            SELECT
                id,
                barcode,
                product_name,
                cost_price,
                retail_price,
                stock_quantity,
                (cost_price * stock_quantity) as total_cost_value,
                (retail_price * stock_quantity) as total_retail_value,
                (retail_price - cost_price) as unit_profit,
                ((retail_price - cost_price) * stock_quantity) as total_profit_potential,
                ROUND(((retail_price - cost_price) / retail_price * 100), 2) as profit_margin_percent
            FROM products
            WHERE stock_quantity > 0
            ORDER BY total_cost_value DESC
        ";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $error) {
            throw new Exception('Failed to get stock valuation report: ' . $error->getMessage());
        }
    }

    /*
    ==========================================
    ----- Inventory Reconciliation -----
    ==========================================
    */
    public function performInventoryReconciliation(
        int $productID,
        int $actualQuantity,
        int $userID,
        ?string $notes = null
    ): array {
        try {
            // Get current system stock
            $sql = "SELECT stock_quantity FROM products WHERE id = :productID";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':productID' => $productID]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                throw new Exception('Product not found.');
            }

            $systemStock = $product['stock_quantity'];
            $discrepancy = $actualQuantity - $systemStock;

            if ($discrepancy !== 0) {
                // Record the reconciliation
                $this->adjustStock(
                    $productID,
                    $discrepancy,
                    'ADJUSTMENT',
                    $userID,
                    'Inventory reconciliation: ' . ($notes ?? '')
                );
            }

            return [
                'productID' => $productID,
                'systemStock' => $systemStock,
                'actualCount' => $actualQuantity,
                'discrepancy' => $discrepancy,
                'reconciled' => $discrepancy === 0,
                'notes' => $notes
            ];

        } catch (Exception $error) {
            throw new Exception('Failed to reconcile inventory: ' . $error->getMessage());
        }
    }

    /*
    ==========================================
    ----- Get Low Stock Products -----
    ==========================================
    */
    public function getLowStockProducts(): array
    {
        $sql = "
            SELECT
                id,
                barcode,
                product_name,
                stock_quantity,
                alert_quantity,
                (alert_quantity - stock_quantity) as units_needed,
                cost_price,
                (cost_price * (alert_quantity - stock_quantity)) as reorder_cost
            FROM products
            WHERE stock_quantity <= alert_quantity
            ORDER BY units_needed DESC
        ";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $error) {
            throw new Exception('Failed to get low stock products: ' . $error->getMessage());
        }
    }

    /*
    ==========================================
    ----- Get Out of Stock Products -----
    ==========================================
    */
    public function getOutOfStockProducts(): array
    {
        $sql = "
            SELECT
                id,
                barcode,
                product_name,
                alert_quantity,
                cost_price,
                (cost_price * alert_quantity) as reorder_cost
            FROM products
            WHERE stock_quantity = 0
            ORDER BY product_name ASC
        ";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $error) {
            throw new Exception('Failed to get out of stock products: ' . $error->getMessage());
        }
    }

    /*
    ==========================================
    ----- Get Slow Moving Items -----
    ==========================================
    */
    public function getSlowMovingItems(
        int $daysThreshold = 30
    ): array {
        $sql = "
            SELECT
                p.id,
                p.barcode,
                p.product_name,
                p.stock_quantity,
                p.alert_quantity,
                COUNT(sm.id) as movement_count,
                MAX(sm.created_at) as last_movement,
                DATEDIFF(NOW(), COALESCE(MAX(sm.created_at), p.created_at)) as days_without_movement
            FROM products p
            LEFT JOIN stock_movements sm ON p.id = sm.product_id
                AND sm.created_at >= DATE_SUB(NOW(), INTERVAL :daysThreshold DAY)
            GROUP BY p.id, p.barcode, p.product_name, p.stock_quantity, p.alert_quantity
            HAVING movement_count = 0 OR movement_count < 3
            ORDER BY days_without_movement DESC
        ";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':daysThreshold', $daysThreshold, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $error) {
            throw new Exception('Failed to get slow moving items: ' . $error->getMessage());
        }
    }

    /*
    ==========================================
    ----- Get Inventory by Category -----
    ==========================================
    */
    public function getInventoryByCategory(): array
    {
        $sql = "
            SELECT
                unit as category,
                COUNT(id) as product_count,
                SUM(stock_quantity) as total_units,
                SUM(cost_price * stock_quantity) as total_cost_value,
                AVG(stock_quantity) as avg_stock_per_product
            FROM products
            GROUP BY unit
            ORDER BY total_cost_value DESC
        ";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $error) {
            throw new Exception('Failed to get inventory by category: ' . $error->getMessage());
        }
    }
}
