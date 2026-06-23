<?php

require_once __DIR__ . '/saleDetail.php';
require_once __DIR__ . '/../config/database.php';

class SaleModel
{
    private PDO $conn;

    private int $userID;

    private array $items = [];

    private float $totalAmount = 0.00;

    public function __construct(int $userID, ?PDO $conn = null)
    {
        $this->conn = $conn ?? (new Database())->connect();

        if (!$this->conn) {
            throw new Exception(
                'Database connection failed.'
            );
        }

        $this->userID = $userID;
    }

    /*
    ==========================
    -------- Add Item --------
    ==========================
    */
    public function addItem(
        SaleDetailModel $item
    ): void {

        $this->items[] = $item;

        $this->calculateTotal();
    }

    /*
    ==========================
    ----- Calculate Total ----
    ==========================
    */
    public function calculateTotal(): float
    {
        $total = 0.00;

        foreach ($this->items as $item) {

            $total += $item->getSubTotal();
        }

        $this->totalAmount = $total;

        return $this->totalAmount;
    }

    /*
    ==========================
    ------- Create Sale ------
    ==========================
    */
    public function createSale(): int
    {
        $sql = "
            INSERT INTO sales
            (
                id_user,
                total_amount,
                status
            )
            VALUES
            (
                :userID,
                :totalAmount,
                'completed'
            )
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':userID'      => $this->userID,
            ':totalAmount' => $this->totalAmount
        ]);

        return (int) $this->conn->lastInsertId();
    }
    /*
==========================
---- Save Sale Details ----
==========================
*/
    public function saveSaleDetails(
        int $saleID
    ): void {

        $sql = "
        INSERT INTO sale_details
        (
            sale_id,
            product_id,
            quantity,
            unit_price,
            subtotal
        )
        VALUES
        (
            :saleID,
            :productID,
            :quantity,
            :unitPrice,
            :subtotal
        )
    ";

        $stmt = $this->conn->prepare($sql);

        foreach ($this->items as $item) {

            $stmt->execute([
                ':saleID'    => $saleID,
                ':productID' => $item->getProductID(),
                ':quantity'  => $item->getQuantity(),
                ':unitPrice' => $item->getUnitPrice(),
                ':subtotal'  => $item->getSubTotal()
            ]);
        }
    }

    /*
    ==========================
    -------- Getters ---------
    ==========================
    */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }

    public function getUserID(): int
    {
        return $this->userID;
    }
}
