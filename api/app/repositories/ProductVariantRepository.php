<?php
class ProductVariantRepository {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function findAll() {
        $query = "SELECT * FROM product_variants WHERE (is_deleted = 0 OR is_deleted IS NULL) ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByProductId($productId) {
        $query = "SELECT * FROM product_variants 
                  WHERE product_id = :product_id AND (is_deleted = 0 OR is_deleted IS NULL) 
                  ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $query = "SELECT * FROM product_variants WHERE id = :id AND (is_deleted = 0 OR is_deleted IS NULL)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO product_variants (product_id, size, color, stock_quantity, is_deleted) 
                  VALUES (:product_id, :size, :color, :stock_quantity, 0)";
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':product_id', $data['product_id'], PDO::PARAM_INT);
        $stmt->bindParam(':size', $data['size'], PDO::PARAM_STR);
        $stmt->bindParam(':color', $data['color'], PDO::PARAM_STR);
        $stmt->bindParam(':stock_quantity', $data['stock_quantity'], PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function update($id, $data) {
        $query = "UPDATE product_variants 
                  SET product_id = :product_id, 
                      size = :size, 
                      color = :color, 
                      stock_quantity = :stock_quantity 
                  WHERE id = :id";
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $data['product_id'], PDO::PARAM_INT);
        $stmt->bindParam(':size', $data['size'], PDO::PARAM_STR);
        $stmt->bindParam(':color', $data['color'], PDO::PARAM_STR);
        $stmt->bindParam(':stock_quantity', $data['stock_quantity'], PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function delete($id) {
        $query = "UPDATE product_variants SET is_deleted = 1 WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
