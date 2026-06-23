<?php

require_once __DIR__ . '/../config/database.php';

class SalesReportModel
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
    =========================================
    ----- Get Sales Summary by Period -----
    =========================================
    */
    public function getSalesSummaryByPeriod(
        string $startDate,
        string $endDate
    ): array {
        $sql = "
            SELECT
                DATE(s.created_at) as sale_date,
                COUNT(s.id) as transaction_count,
                SUM(s.total_amount) as total_sales,
                AVG(s.total_amount) as avg_transaction_value,
                MIN(s.total_amount) as min_transaction,
                MAX(s.total_amount) as max_transaction,
                COUNT(DISTINCT s.id_user) as unique_cashiers
            FROM sales s
            WHERE DATE(s.created_at) BETWEEN :startDate AND :endDate
            GROUP BY DATE(s.created_at)
            ORDER BY sale_date DESC
        ";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':startDate' => $startDate,
                ':endDate' => $endDate
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $error) {
            throw new Exception('Failed to get sales summary: ' . $error->getMessage());
        }
    }

    /*
    ==========================================
    ----- Get Overall Sales Summary -----
    ==========================================
    */
    public function getOverallSalesSummary(
        string $startDate,
        string $endDate
    ): array {
        $sql = "
            SELECT
                COUNT(s.id) as total_transactions,
                SUM(s.total_amount) as total_revenue,
                AVG(s.total_amount) as avg_transaction_value,
                MIN(s.total_amount) as min_transaction,
                MAX(s.total_amount) as max_transaction,
                COUNT(DISTINCT s.id_user) as total_cashiers,
                COUNT(DISTINCT DATE(s.created_at)) as days_with_sales,
                DATEDIFF(:endDate, :startDate) + 1 as total_days_range
            FROM sales s
            WHERE DATE(s.created_at) BETWEEN :startDate AND :endDate
        ";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':startDate' => $startDate,
                ':endDate' => $endDate
            ]);
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (Exception $error) {
            throw new Exception('Failed to get overall sales summary: ' . $error->getMessage());
        }
    }

    /*
    ==========================================
    ----- Get Sales by Product -----
    ==========================================
    */
    public function getSalesByProduct(
        string $startDate,
        string $endDate,
        int $limit = 50,
        int $offset = 0
    ): array {
        $sql = "
            SELECT
                p.id,
                p.barcode,
                p.product_name,
                SUM(sd.quantity) as total_quantity_sold,
                SUM(sd.subtotal) as total_revenue,
                AVG(sd.unit_price) as avg_unit_price,
                COUNT(DISTINCT sd.sale_id) as transaction_count,
                (SUM(sd.subtotal) / SUM(sd.quantity)) as avg_selling_price
            FROM sale_details sd
            JOIN products p ON sd.product_id = p.id
            JOIN sales s ON sd.sale_id = s.id
            WHERE DATE(s.created_at) BETWEEN :startDate AND :endDate
            GROUP BY p.id, p.barcode, p.product_name
            ORDER BY total_revenue DESC
            LIMIT :limit OFFSET :offset
        ";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':startDate', $startDate);
            $stmt->bindValue(':endDate', $endDate);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $error) {
            throw new Exception('Failed to get sales by product: ' . $error->getMessage());
        }
    }

    /*
    ==========================================
    ----- Get Top Selling Products -----
    ==========================================
    */
    public function getTopSellingProducts(
        string $startDate,
        string $endDate,
        int $limit = 10
    ): array {
        $sql = "
            SELECT
                p.id,
                p.barcode,
                p.product_name,
                p.cost_price,
                SUM(sd.quantity) as total_quantity,
                SUM(sd.subtotal) as total_revenue,
                SUM(sd.subtotal) - SUM(p.cost_price * sd.quantity) as total_profit
            FROM sale_details sd
            JOIN products p ON sd.product_id = p.id
            JOIN sales s ON sd.sale_id = s.id
            WHERE DATE(s.created_at) BETWEEN :startDate AND :endDate
            GROUP BY p.id, p.barcode, p.product_name, p.cost_price
            ORDER BY total_quantity DESC
            LIMIT :limit
        ";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':startDate', $startDate);
            $stmt->bindValue(':endDate', $endDate);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $error) {
            throw new Exception('Failed to get top selling products: ' . $error->getMessage());
        }
    }

    /*
    ==========================================
    ----- Get Sales by Cashier/User -----
    ==========================================
    */
    public function getSalesByCashier(
        string $startDate,
        string $endDate
    ): array {
        $sql = "
            SELECT
                u.id,
                u.username,
                u.email,
                COUNT(s.id) as transaction_count,
                SUM(s.total_amount) as total_sales,
                AVG(s.total_amount) as avg_transaction,
                MIN(s.total_amount) as min_transaction,
                MAX(s.total_amount) as max_transaction,
                COUNT(DISTINCT DATE(s.created_at)) as active_days
            FROM sales s
            JOIN users u ON s.id_user = u.id
            WHERE DATE(s.created_at) BETWEEN :startDate AND :endDate
            GROUP BY u.id, u.username, u.email
            ORDER BY total_sales DESC
        ";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':startDate' => $startDate,
                ':endDate' => $endDate
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $error) {
            throw new Exception('Failed to get sales by cashier: ' . $error->getMessage());
        }
    }

    /*
    ==========================================
    ----- Get Payment Method Summary -----
    ==========================================
    */
    public function getPaymentMethodSummary(
        string $startDate,
        string $endDate
    ): array {
        $sql = "
            SELECT
                p.payment_method,
                p.payment_status,
                COUNT(p.id) as transaction_count,
                SUM(p.amount_paid) as total_amount,
                AVG(p.amount_paid) as avg_amount,
                SUM(CASE WHEN p.payment_status = 'COMPLETED' THEN 1 ELSE 0 END) as completed_count,
                SUM(CASE WHEN p.payment_status = 'FAILED' THEN 1 ELSE 0 END) as failed_count
            FROM payments p
            JOIN sales s ON p.sale_id = s.id
            WHERE DATE(s.created_at) BETWEEN :startDate AND :endDate
            GROUP BY p.payment_method, p.payment_status
            ORDER BY total_amount DESC
        ";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':startDate' => $startDate,
                ':endDate' => $endDate
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $error) {
            throw new Exception('Failed to get payment method summary: ' . $error->getMessage());
        }
    }

    /*
    ==========================================
    ----- Get Hourly Sales Distribution -----
    ==========================================
    */
    public function getHourlySalesDistribution(
        string $date
    ): array {
        $sql = "
            SELECT
                HOUR(s.created_at) as hour,
                COUNT(s.id) as transaction_count,
                SUM(s.total_amount) as total_sales,
                AVG(s.total_amount) as avg_transaction,
                COUNT(DISTINCT s.id_user) as cashiers
            FROM sales s
            WHERE DATE(s.created_at) = :date
            GROUP BY HOUR(s.created_at)
            ORDER BY hour ASC
        ";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':date' => $date]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $error) {
            throw new Exception('Failed to get hourly sales distribution: ' . $error->getMessage());
        }
    }

    /*
    ==========================================
    ----- Get Sales Trend -----
    ==========================================
    */
    public function getSalesTrend(
        string $startDate,
        string $endDate,
        string $interval = 'day' // day, week, month
    ): array {
        $groupFormat = match ($interval) {
            'week' => '%Y-W%v',
            'month' => '%Y-%m',
            default => '%Y-%m-%d'
        };

        $sql = "
            SELECT
                DATE_FORMAT(s.created_at, :groupFormat) as period,
                COUNT(s.id) as transaction_count,
                SUM(s.total_amount) as total_sales,
                AVG(s.total_amount) as avg_transaction
            FROM sales s
            WHERE DATE(s.created_at) BETWEEN :startDate AND :endDate
            GROUP BY DATE_FORMAT(s.created_at, :groupFormat)
            ORDER BY period ASC
        ";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':startDate' => $startDate,
                ':endDate' => $endDate,
                ':groupFormat' => $groupFormat
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $error) {
            throw new Exception('Failed to get sales trend: ' . $error->getMessage());
        }
    }

    /*
    ==========================================
    ----- Get Product Category Sales -----
    ==========================================
    */
    public function getProductCategorySales(
        string $startDate,
        string $endDate
    ): array {
        $sql = "
            SELECT
                p.unit as category,
                COUNT(DISTINCT sd.product_id) as unique_products,
                SUM(sd.quantity) as total_quantity,
                SUM(sd.subtotal) as total_revenue,
                AVG(sd.unit_price) as avg_unit_price,
                COUNT(DISTINCT sd.sale_id) as transaction_count
            FROM sale_details sd
            JOIN products p ON sd.product_id = p.id
            JOIN sales s ON sd.sale_id = s.id
            WHERE DATE(s.created_at) BETWEEN :startDate AND :endDate
            GROUP BY p.unit
            ORDER BY total_revenue DESC
        ";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':startDate' => $startDate,
                ':endDate' => $endDate
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $error) {
            throw new Exception('Failed to get product category sales: ' . $error->getMessage());
        }
    }

    /*
    ==========================================
    ----- Get Customer Purchase Behavior -----
    ==========================================
    */
    public function getCustomerBehavior(
        string $startDate,
        string $endDate
    ): array {
        $sql = "
            SELECT
                COUNT(DISTINCT s.id_user) as unique_customers,
                COUNT(s.id) as total_transactions,
                AVG(s.total_amount) as avg_transaction_value,
                MAX(s.total_amount) as max_transaction_value,
                MIN(s.total_amount) as min_transaction_value,
                ROUND(COUNT(s.id) / COUNT(DISTINCT s.id_user), 2) as avg_transactions_per_customer,
                SUM(s.total_amount) as total_sales
            FROM sales s
            WHERE DATE(s.created_at) BETWEEN :startDate AND :endDate
        ";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':startDate' => $startDate,
                ':endDate' => $endDate
            ]);
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (Exception $error) {
            throw new Exception('Failed to get customer behavior: ' . $error->getMessage());
        }
    }

    /*
    ==========================================
    ----- Get Low Revenue Products -----
    ==========================================
    */
    public function getLowRevenueProducts(
        string $startDate,
        string $endDate,
        int $limit = 10
    ): array {
        $sql = "
            SELECT
                p.id,
                p.barcode,
                p.product_name,
                SUM(sd.quantity) as total_quantity_sold,
                SUM(sd.subtotal) as total_revenue,
                COUNT(DISTINCT sd.sale_id) as transaction_count,
                ROUND((SUM(sd.subtotal) / SUM(sd.quantity)), 2) as avg_selling_price
            FROM sale_details sd
            JOIN products p ON sd.product_id = p.id
            JOIN sales s ON sd.sale_id = s.id
            WHERE DATE(s.created_at) BETWEEN :startDate AND :endDate
            GROUP BY p.id, p.barcode, p.product_name
            ORDER BY total_revenue ASC
            LIMIT :limit
        ";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':startDate', $startDate);
            $stmt->bindValue(':endDate', $endDate);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $error) {
            throw new Exception('Failed to get low revenue products: ' . $error->getMessage());
        }
    }

    /*
    ==========================================
    ----- Get Sales Performance Index -----
    ==========================================
    */
    public function getSalesPerformanceIndex(
        string $startDate,
        string $endDate,
        string $previousStartDate,
        string $previousEndDate
    ): array {
        // Current period
        $currentSQL = "
            SELECT COUNT(s.id) as transaction_count, SUM(s.total_amount) as total_sales
            FROM sales s
            WHERE DATE(s.created_at) BETWEEN :startDate AND :endDate
        ";

        // Previous period
        $previousSQL = "
            SELECT COUNT(s.id) as transaction_count, SUM(s.total_amount) as total_sales
            FROM sales s
            WHERE DATE(s.created_at) BETWEEN :prevStart AND :prevEnd
        ";

        try {
            $stmtCurrent = $this->conn->prepare($currentSQL);
            $stmtCurrent->execute([
                ':startDate' => $startDate,
                ':endDate' => $endDate
            ]);
            $current = $stmtCurrent->fetch(PDO::FETCH_ASSOC);

            $stmtPrevious = $this->conn->prepare($previousSQL);
            $stmtPrevious->execute([
                ':prevStart' => $previousStartDate,
                ':prevEnd' => $previousEndDate
            ]);
            $previous = $stmtPrevious->fetch(PDO::FETCH_ASSOC);

            $salesGrowth = $previous['total_sales'] > 0 
                ? (($current['total_sales'] - $previous['total_sales']) / $previous['total_sales'] * 100)
                : 0;

            $transactionGrowth = $previous['transaction_count'] > 0
                ? (($current['transaction_count'] - $previous['transaction_count']) / $previous['transaction_count'] * 100)
                : 0;

            return [
                'current_period' => [
                    'total_sales' => $current['total_sales'],
                    'transaction_count' => $current['transaction_count']
                ],
                'previous_period' => [
                    'total_sales' => $previous['total_sales'],
                    'transaction_count' => $previous['transaction_count']
                ],
                'sales_growth_percent' => round($salesGrowth, 2),
                'transaction_growth_percent' => round($transactionGrowth, 2),
                'trend' => $salesGrowth >= 0 ? 'UP' : 'DOWN'
            ];

        } catch (Exception $error) {
            throw new Exception('Failed to get sales performance index: ' . $error->getMessage());
        }
    }
}
