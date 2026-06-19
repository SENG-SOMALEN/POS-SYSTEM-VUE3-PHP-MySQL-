<?php
require_once __DIR__. '/CartItem.php';
class CartModel {
    private $items = [];
    private $totalAmount = 0.00;
    
    public function addItem(CartItemModel $item): void {
        foreach ($this->items as $existing) {
            if($existing->getProductID() === $item->getProductID()) {
                $existing->setQuantity($existing->getQuantity() + $item->getQuantity());

                $this->calculateTotal();
                return;
            }
        }

        $this->items[] = $item;
        $this->calculateTotal();    
    }

    public function updateQuantity(int $productID, int $quantity): void {
        foreach ($this->items as $item)  {
            if ($item->getProductID() === $productID) {
                $item->setQuantity($quantity);

                $this->calculateTotal();
                return;
            }
        }

        throw new \InvalidArgumentException("Product ID $productID not found in cart.");
    }

    public function removeItem(int $productID): void {
        foreach ($this->items as $index => $item) {
            if ($item->getProductID() === $productID) {
                unset($this->items[$index]);

                $this->items = array_values($this->items);
                $this->calculateTotal();
                return;
            }
        }

        throw new \InvalidArgumentException("Product ID $productID not found in cart.");
    }

    public function calculateTotal(): float {
        $total = 0;

        foreach ($this->items as $item) {
            $total += $item->getSubTotal();
        }

        $this->totalAmount = $total;
        return $this->totalAmount;
    }

    public function getItems(): array {
        return $this->items;
    }

    public function getTotalAmount(): float {
        return $this->calculateTotal();
    }
}