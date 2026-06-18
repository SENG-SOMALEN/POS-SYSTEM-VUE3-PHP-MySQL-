<?php

require_once __DIR__ . "/../config/database.php";

class ProductModel {
    private $conn;

    public function __construct() {
        $database = new Database();

        $this->conn = $database->connect();

        if(!$this->conn) {
            throw new Exception("Database connection failed");
        }
    }

    /*
    =======================
    | Query get all product
    =======================
    */
    public function getAllProduct($limit = 20, $offset = 0): array {
        $getAllQuery = "SELECT * FROM products LIMIT :limit OFFSET :offset";

        try {
            $stmt = $this->conn->prepare($getAllQuery);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(PDOException $error) {
            return [
                'error' => $error->getMessage()
            ];
        }
        
    }

    /*
    =======================
    | Query get product by id
    =======================
    */
    public function getProductByID($id): array|false{
        $getIdQuery = "SELECT * FROM products WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($getIdQuery);
            $stmt->execute([
                ':id' => $id
            ]);

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch(PDOException $error) {
            return [
                'error' => $error->getMessage()
            ];
        }
    }

    /*
    =======================
    | Query create product
    =======================
    */
    public function createProduct($data): bool {
        $createQuery = "INSERT INTO products(barcode, product_name, cost_price, retail_price, stock_quantity, alert_quantity, unit, image_url)
                        VALUES(:barcode, :product_name, :cost_price, :retail_price, :stock_quantity, :alert_quantity, :unit, :image_url)
        ";

        try {
            $stmt = $this->conn->prepare($createQuery);
            $stmt->execute([
                ':barcode' => $data['barcode'],
                ':product_name' => $data['product_name'],
                ':cost_price' => $data['cost_price'],
                ':retail_price' => $data['retail_price'],
                ':stock_quantity' => $data['stock_quantity'],
                ':alert_quantity' => $data['alert_quantity'],
                ':unit' => $data['unit'],
                ':image_url' => $data['image_url']
            ]);

            return $stmt->rowCount() > 0;

        } catch(PDOException $error) {
            return false;
        }
    }

    /*
    =======================
    | Query update product
    =======================
    */
    public function updateProduct($id, $data): bool{
        $updateQuery = "UPDATE products
                        SET barcode = :barcode,
                            product_name = :product_name,
                            cost_price = :cost_price,
                            retail_price = :retail_price,
                            stock_quantity = :stock_quantity,
                            alert_quantity = :alert_quantity,
                            unit = :unit,
                            image_url = :image_url
                        WHERE id = :id
        ";

        try {
            $stmt = $this->conn->prepare($updateQuery);
            $stmt->execute([
                ':barcode' => $data['barcode'],
                ':product_name' => $data['product_name'],
                ':cost_price' => $data['cost_price'],
                ':retail_price' => $data['retail_price'],
                ':stock_quantity' => $data['stock_quantity'],
                ':alert_quantity' => $data['alert_quantity'],
                ':unit' => $data['unit'],
                ':image_url' => $data['image_url'],
                ':id' => $id       
            ]);

            return $stmt->rowCount() > 0;

        } catch(PDOException $error) {
            return false;
        }
    }

    /*
    =======================
    | Query delete product
    =======================
    */
    public function deleteProduct($id): bool {
        $deleteQuery = "DELETE FROM products WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($deleteQuery);
            $stmt->execute([
                ':id' => $id
            ]);

            return $stmt->rowCount() > 0;

        } catch(PDOException $error) {
            return false;
        }
    }

    /*
    =======================
    | Query search product
    =======================
    */
    public function searchProduct($keyword): array {
        $searchQuery = "SELECT id, barcode, product_name, cost_price, retail_price, stock_quantity, alert_quantity, unit, image_url
                        FROM products
                        WHERE product_name LIKE :search_query OR barcode LIKE :search_query;
        ";
        $searchString = "%". $keyword . "%";

        try {
            $stmt = $this->conn->prepare($searchQuery);
            $stmt->execute([
                ':search_query' => $searchString
            ]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(PDOException $error) {
            return [
                'error' => $error->getMessage()
            ];
        }
    }
}