<?php

class SaleDetailModel
{
    private int $productID;

    private int $quantity;

    private float $unitPrice;

    private float $subTotal;

    public function __construct(
        int $productID,
        int $quantity,
        float $unitPrice
    ) {

        $this->productID = $productID;

        $this->quantity = $quantity;

        $this->unitPrice = $unitPrice;

        $this->subTotal =
            $this->calculateSubTotal();
    }

    /*
    ==========================
    --- Calculate Subtotal ---
    ==========================
    */
    public function calculateSubTotal(): float
    {
        return $this->quantity
            * $this->unitPrice;
    }

    /*
    ==========================
    -------- Getters ---------
    ==========================
    */
    public function getProductID(): int
    {
        return $this->productID;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getUnitPrice(): float
    {
        return $this->unitPrice;
    }

    public function getSubTotal(): float
    {
        return $this->subTotal;
    }
}