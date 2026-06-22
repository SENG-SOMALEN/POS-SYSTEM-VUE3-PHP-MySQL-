<?php
class CartItemModel
{
    private $productID;
    private $productName;
    private $unitPrice;
    private $quantity;
    private $subTotal;


    public function __construct(
        int $productID,
        string $productName,
        float $unitPrice,
        int $quantity
    ) {

        if ($quantity < 1) {
            throw new InvalidArgumentException(
                "Quantity must be at least 1."
            );
        }

        if ($unitPrice < 0) {
            throw new InvalidArgumentException(
                "Unit price cannot be negative."
            );
        }

        $this->productID = $productID;
        $this->productName = $productName;
        $this->unitPrice = $unitPrice;
        $this->quantity = $quantity;

        $this->subTotal = $this->calculateSubTotal();
    }

    public function calculateSubTotal(): float
    {
        return $this->unitPrice * $this->quantity;
    }

    public function getProductID(): int
    {
        return $this->productID;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getUnitPrice(): float
    {
        return $this->unitPrice;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getSubTotal(): float
    {
        return $this->subTotal;
    }

    public function setQuantity(int $quantity): void
    {
        if ($quantity < 1) {
            throw new \InvalidArgumentException("Quantity must be at least 1.");
        }

        $this->quantity = $quantity;
        $this->subTotal = $this->calculateSubTotal();
    }
}
