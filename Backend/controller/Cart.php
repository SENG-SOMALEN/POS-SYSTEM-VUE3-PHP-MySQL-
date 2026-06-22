<?php

require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/CartItem.php';

class CartController
{
    private CartModel $cart;

    public function __construct()
    {
        $this->cart = new CartModel();
    }

    /*
    ==========================
    -------- Get Cart --------
    ==========================
    */
    public function getCart(): void
    {
        echo json_encode([
            'success' => true,
            'cart' => $this->cart->getItems(),
            'total' => $this->cart->calculateTotal()
        ]);
    }

    /*
    ==========================
    -------- Add Item --------
    ==========================
    */
    public function addItem(): void
    {
        $data = json_decode(
            file_get_contents('php://input'),
            true
        );

        if (!$data) {
            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Invalid JSON'
            ]);

            return;
        }

        try {

            $item = new CartItemModel(
                (int)$data['productID'],
                (string)$data['productName'],
                (float)$data['unitPrice'],
                (int)$data['quantity']
            );

            $this->cart->addItem($item);

            echo json_encode([
                'success' => true,
                'message' => 'Item added successfully'
            ]);

        } catch (Exception $e) {

            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /*
    ==========================
    ------ Update Item -------
    ==========================
    */
    public function updateQuantity(): void
    {
        $data = json_decode(
            file_get_contents('php://input'),
            true
        );

        if (
            !isset($data['productID']) ||
            !isset($data['quantity'])
        ) {

            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Missing required fields'
            ]);

            return;
        }

        try {

            $this->cart->updateQuantity(
                (int)$data['productID'],
                (int)$data['quantity']
            );

            echo json_encode([
                'success' => true,
                'message' => 'Quantity updated successfully'
            ]);

        } catch (Exception $e) {

            http_response_code(404);

            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /*
    ==========================
    ------- Remove Item ------
    ==========================
    */
    public function removeItem(): void
    {
        $data = json_decode(
            file_get_contents('php://input'),
            true
        );

        if (!isset($data['productID'])) {

            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Product ID is required'
            ]);

            return;
        }

        try {

            $this->cart->removeItem(
                (int)$data['productID']
            );

            echo json_encode([
                'success' => true,
                'message' => 'Item removed successfully'
            ]);

        } catch (Exception $e) {

            http_response_code(404);

            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /*
    ==========================
    ------- Clear Cart -------
    ==========================
    */
    public function clearCart(): void
    {
        $this->cart->clearCart();

        echo json_encode([
            'success' => true,
            'message' => 'Cart cleared successfully'
        ]);
    }
}