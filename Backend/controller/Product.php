<?php

require_once __DIR__ . "/../models/Product.php";

class ProductController {
    private $product;

    public function __construct() {
        $this->product = new ProductModel();
    }

    /*
    =======================
    | logic get all product
    =======================
    */
    public function getProductAll(){
        $getData = $this->product->getAllProduct();

        if (!$getData) {
            http_response_code(404);

            echo json_encode([
                'success' => false,
                'message' => 'No product found'
            ]);

            return;
        }

        echo json_encode([
            'success' => true,
            'data' => $getData
        ]);
    }

    /*
    =======================
    | logic get Id product
    =======================
    */
    public function getIDproduct($id){
        if (!is_numeric($id)) {
            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'invalid id'
            ]);

            return;
        }

        $getdata = $this->product->getProductByID($id);

        if (!$getdata) {
            http_response_code(404);

            echo json_encode([
                'success' => false,
                'message' => 'Not found product'
            ]);

            return;
        }

        echo json_encode([
            'success' => true,
            'data' => $getdata
        ]);
    }

    /*
    =======================
    | logic create product
    =======================
    */
    public function postProduct(){  
        $postData = json_decode(
            file_get_contents("php://input"),
            true
        );
        if (!$postData) {
            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Invalid product data'
            ]);

            return;
        }

        $result = $this->product->createProduct($postData);

        if (!$result) {
            http_response_code(500);

            echo json_encode([
                'success' => false,
                'message' => 'Failed to create product'
            ]);

            return;
        }

        echo json_encode([
            'success' => true,
            'message' => 'Product created successful'
        ]);
    }

    /*
    =======================
    | logic update product
    =======================
    */
    public function putProduct($id){
        if(!is_numeric($id)) {
            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Invalid id'
            ]);

            return;
        }

        $putData = json_decode(file_get_contents("php://input"), true);
        if (!$putData) {
            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Invalid update data'
            ]);

            return;
        }

        $result = $this->product->updateProduct($id, $putData);

        if (!$result) {
            http_response_code(500);

            echo json_encode([
                'success' => false,
                'message' => 'Failed to update product or data unchanged'
            ]);

            return;
        }

        echo json_encode([
            'success' => true,
            'message' => 'Product updated successfully'
        ]);
    }

    /*
    =======================
    | logic delete product
    =======================
    */
    public function deleteProduct($id){
        if(!is_numeric($id)) {
            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Invalid id'
            ]);

            return;
        }

        $result = $this->product->deleteProduct($id);

        if (!$result) {
            http_response_code(404);

            echo json_encode([
                'success' => false,
                'message' => 'Product not found or already deleted'
            ]);

            return;
        }

        echo json_encode([
            'success' => true,
            'message' => 'Product deleted successfully'
        ]);
    }

    /*
    =======================
    | logic search product
    =======================
    */
    public function searchProduct($keyword){
        if (empty(trim($keyword))) {
            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Search keyword cannot be empty'
            ]);

            return;
        }

        $result = $this->product->searchProduct($keyword);
        if (!$result) {
            http_response_code(404);

            echo json_encode([
                'success' => false,
                'message' => 'No products matched'
            ]);
        }

        echo json_encode([
            'success' => true,
            'data' => $result
        ]);
    }
}
