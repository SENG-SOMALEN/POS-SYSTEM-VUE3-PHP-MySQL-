<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/sale.php';
require_once __DIR__ . '/../models/saleDetail.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Receipt.php';

class CheckoutController
{
    public function checkout(): void
    {
        header('Content-Type: application/json');

        if (empty($_SESSION['cart'])) {

            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Cart is empty'
            ]);

            return;
        }

        if (!isset($_SESSION['user_id'])) {

            http_response_code(401);

            echo json_encode([
                'success' => false,
                'message' => 'User not logged in'
            ]);

            return;
        }

        try {
            // ===========================
            // BEGIN DATABASE TRANSACTION
            // ===========================
            $database = new Database();
            $conn = $database->connect();
            $conn->beginTransaction();
            $userID = (int) $_SESSION['user_id'];

            try {
                /*
                ==========================
                -------- Create Sale -----
                ==========================
                */
                $sale = new SaleModel(
                    $userID,
                    $conn
                );

                foreach ($_SESSION['cart'] as $cartItem) {

                    $saleDetail = new SaleDetailModel(
                        $cartItem->getProductID(),
                        $cartItem->getQuantity(),
                        $cartItem->getUnitPrice()
                    );

                    $sale->addItem($saleDetail);
                }

                /*
                ==========================
                -------- Save Sale -------
                ==========================
                */
                $saleID = $sale->createSale();

                /*
                ==========================
                ---- Save Sale Details ---
                ==========================
                */
                $sale->saveSaleDetails(
                    $saleID
                );

                /*
                ==========================
                ------ Save Payment ------
                ==========================
                */
                $payment = new PaymentModel(
                    'CASH',
                    'COMPLETED',
                    $sale->getTotalAmount(),
                    $sale->getTotalAmount(),
                    0,
                    null,
                    null,
                    $conn
                );

                $paymentID = $payment->savePayment(
                    $saleID
                );

                /*
                ==========================
                ------ Save Receipt ------
                ==========================
                */
                $receipt = new ReceiptModel(
                    $userID,
                    $sale->getTotalAmount(),
                    $conn
                );

                $receiptID = $receipt->saveReceipt(
                    $saleID
                );

                /*
                ==========================
                ---- COMMIT TRANSACTION ---
                ==========================
                */
                $conn->commit();

                /*
                ==========================
                ------- Clear Cart -------
                ==========================
                */
                $_SESSION['cart'] = [];

                /*
                ==========================
                -------- Response --------
                ==========================
                */
                echo json_encode([
                    'success'       => true,
                    'message'       => 'Checkout completed',
                    'saleID'        => $saleID,
                    'paymentID'     => $paymentID,
                    'receiptID'     => $receiptID,
                    'receiptNumber' => $receipt->getReceiptNumber(),
                    'userID'        => $sale->getUserID(),
                    'totalAmount'   => $sale->getTotalAmount(),
                    'itemCount'     => count(
                        $sale->getItems()
                    )
                ]);

            } catch (Exception $transactionError) {
                // ===========================
                // ROLLBACK ON ERROR
                // ===========================
                $conn->rollBack();
                throw $transactionError;
            }

        } catch (Exception $error) {

            http_response_code(500);

            echo json_encode([
                'success' => false,
                'message' => $error->getMessage()
            ]);
        }
    }
}
