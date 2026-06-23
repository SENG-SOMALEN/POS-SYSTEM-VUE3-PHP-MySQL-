<?php

require_once __DIR__ . '/../config/database.php';

class PaymentModel
{
    private PDO $conn;

    private string $paymentMethod;
    private string $paymentStatus;
    private ?string $paymentGateway;
    private float $amountPaid;
    private float $amountTendered;
    private float $changeGiven;
    private ?string $transactionReference;

    public function __construct(
        string $paymentMethod,
        string $paymentStatus,
        float $amountPaid,
        float $amountTendered = 0,
        float $changeGiven = 0,
        ?string $paymentGateway = null,
        ?string $transactionReference = null,
        ?PDO $conn = null
    ) {

        $this->conn = $conn ?? (new Database())->connect();

        if (!$this->conn) {
            throw new Exception(
                'Database connection failed.'
            );
        }

        $allowedMethods = [
            'CASH',
            'KHQR',
            'CARD'
        ];

        $allowedStatus = [
            'PENDING',
            'COMPLETED',
            'FAILED',
            'REFUNDED'
        ];

        $paymentMethod = strtoupper(
            $paymentMethod
        );

        $paymentStatus = strtoupper(
            $paymentStatus
        );

        if (
            !in_array(
                $paymentMethod,
                $allowedMethods
            )
        ) {
            throw new InvalidArgumentException(
                'Invalid payment method.'
            );
        }

        if (
            !in_array(
                $paymentStatus,
                $allowedStatus
            )
        ) {
            throw new InvalidArgumentException(
                'Invalid payment status.'
            );
        }

        $this->paymentMethod =
            $paymentMethod;

        $this->paymentStatus =
            $paymentStatus;

        $this->amountPaid =
            $amountPaid;

        $this->amountTendered =
            $amountTendered;

        $this->changeGiven =
            $changeGiven;

        $this->paymentGateway =
            $paymentGateway;

        $this->transactionReference =
            $transactionReference;
    }

    /*
    ==========================
    ------ Save Payment ------
    ==========================
    */
    public function savePayment(
        int $saleID
    ): int {

        $sql = "
            INSERT INTO payments
            (
                sale_id,
                payment_method,
                payment_status,
                payment_gateway,
                amount_paid,
                amount_tendered,
                change_given,
                transaction_reference
            )
            VALUES
            (
                :saleID,
                :paymentMethod,
                :paymentStatus,
                :paymentGateway,
                :amountPaid,
                :amountTendered,
                :changeGiven,
                :transactionReference
            )
        ";

        $stmt = $this->conn->prepare(
            $sql
        );

        $stmt->execute([
            ':saleID' => $saleID,
            ':paymentMethod' =>
                $this->paymentMethod,
            ':paymentStatus' =>
                $this->paymentStatus,
            ':paymentGateway' =>
                $this->paymentGateway,
            ':amountPaid' =>
                $this->amountPaid,
            ':amountTendered' =>
                $this->amountTendered,
            ':changeGiven' =>
                $this->changeGiven,
            ':transactionReference' =>
                $this->transactionReference
        ]);

        return (int)
            $this->conn->lastInsertId();
    }

    /*
    ==========================
    -------- Getters ---------
    ==========================
    */
    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    public function getPaymentStatus(): string
    {
        return $this->paymentStatus;
    }

    public function getPaymentGateway(): ?string
    {
        return $this->paymentGateway;
    }

    public function getAmountPaid(): float
    {
        return $this->amountPaid;
    }

    public function getAmountTendered(): float
    {
        return $this->amountTendered;
    }

    public function getChangeGiven(): float
    {
        return $this->changeGiven;
    }

    public function getTransactionReference(): ?string
    {
        return $this->transactionReference;
    }
}
