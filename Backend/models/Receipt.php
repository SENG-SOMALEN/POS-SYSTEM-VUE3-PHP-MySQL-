<?php

require_once __DIR__ . '/../config/database.php';

class ReceiptModel
{
    private PDO $conn;

    private string $receiptNumber;

    private int $userID;

    private float $totalAmount;

    private string $createdAt;

    public function __construct(
        int $userID,
        float $totalAmount,
        ?PDO $conn = null
    ) {

        $this->conn =
            $conn ?? (new Database())->connect();

        $this->receiptNumber =
            uniqid('REC-');

        $this->userID =
            $userID;

        $this->totalAmount =
            $totalAmount;

        $this->createdAt =
            date('Y-m-d H:i:s');
    }

    /*
    ==========================
    ------ Save Receipt ------
    ==========================
    */
    public function saveReceipt(
        int $saleID
    ): int {

        $sql = "
            INSERT INTO receipts
            (
                sale_id,
                receipt_number,
                total_amount,
                created_at
            )
            VALUES
            (
                :saleID,
                :receiptNumber,
                :totalAmount,
                :createdAt
            )
        ";

        $stmt =
            $this->conn->prepare($sql);

        $stmt->execute([
            ':saleID' =>
                $saleID,

            ':receiptNumber' =>
                $this->receiptNumber,

            ':totalAmount' =>
                $this->totalAmount,

            ':createdAt' =>
                $this->createdAt
        ]);

        return (int)
            $this->conn->lastInsertId();
    }

    /*
    ==========================
    -------- Getters ---------
    ==========================
    */
    public function getReceiptNumber(): string
    {
        return $this->receiptNumber;
    }

    public function getUserID(): int
    {
        return $this->userID;
    }

    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
}
