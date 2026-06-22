<?php

require_once __DIR__ . '/CartItem.php';

class CartModel
{
    public function __construct()
    {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    /*  
    ==========================
    -------- Add Item --------
    ==========================
    */
    public function addItem(CartItemModel $item): void
    {
        foreach ($_SESSION['cart'] as $existing) {

            if (
                $existing->getProductID()
                === $item->getProductID()
            ) {

                $existing->setQuantity(
                    $existing->getQuantity()
                    + $item->getQuantity()
                );

                return;
            }
        }

        $_SESSION['cart'][] = $item;
    }

    /*
    ==========================
    ------ Update Item -------
    ==========================
    */
    public function updateQuantity(int $productID, int $quantity): void
    {
        foreach ($_SESSION['cart'] as $item) {

            if (
                $item->getProductID()
                === $productID
            ) {

                $item->setQuantity($quantity);

                return;
            }
        }

        throw new InvalidArgumentException(
            "Product ID $productID not found."
        );
    }

    /*
    ==========================
    ------- Remove Item ------
    ==========================
    */
    public function removeItem(int $productID): void
    {
        foreach (
            $_SESSION['cart'] as $index => $item
        ) {

            if (
                $item->getProductID()
                === $productID
            ) {

                unset(
                    $_SESSION['cart'][$index]
                );

                $_SESSION['cart']
                    = array_values(
                        $_SESSION['cart']
                    );

                return;
            }
        }

        throw new InvalidArgumentException(
            "Product ID $productID not found."
        );
    }

    /*
    ==========================
    - Calculate Total Item ---
    ==========================
    */
    public function calculateTotal(): float
    {
        $total = 0.00;

        foreach ($_SESSION['cart'] as $item) {

            $total += $item->getSubTotal();
        }

        return $total;
    }

    /*
    ==========================
    -------- Get Cart --------
    ==========================
    */
    public function getItems(): array
    {
        return $_SESSION['cart'];
    }

    /*
    ==========================
    ------- Clear Cart -------
    ==========================
    */
    public function clearCart(): void
    {
        $_SESSION['cart'] = [];
    }
}