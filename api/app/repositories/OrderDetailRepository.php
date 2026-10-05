<?php
class OrderDetailRepository {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function findAll() {
        $stmt = $this->db->prepare("SELECT * FROM order_details WHERE (is_deleted = 0 OR is_deleted IS NULL) ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM order_details WHERE id = :id AND (is_deleted = 0 OR is_deleted IS NULL)");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByOrderId($orderId) {
        $stmt = $this->db->prepare("SELECT * FROM order_details WHERE order_id = :order_id AND (is_deleted = 0 OR is_deleted IS NULL)");
        $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO order_details (order_id, product_variant_id, quantity, unit_price, is_deleted)
            VALUES (:order_id, :product_variant_id, :quantity, :unit_price, 0)
        ");
        $stmt->bindParam(':order_id',           $data['order_id']);
        $stmt->bindParam(':product_variant_id', $data['product_variant_id']);
        $stmt->bindParam(':quantity',           $data['quantity']);
        $stmt->bindParam(':unit_price',         $data['unit_price']);
        $stmt->execute();

        $lastId = $this->db->lastInsertId();
        return $this->findById($lastId);
    }

    public function update($id, $data) {
        $fields = [];
        $values = [];
        $allowedColumns = ['order_id', 'product_variant_id', 'quantity', 'unit_price', 'is_deleted'];

        foreach ($allowedColumns as $col) {
            if (array_key_exists($col, $data)) {
                $fields[] = "$col = :$col";
                $values[":$col"] = $data[$col];
            }
        }
        if (empty($fields)) return $this->findById($id);

        $values[':id'] = $id;
        $stmt = $this->db->prepare("UPDATE order_details SET " . implode(', ', $fields) . " WHERE id = :id");
        $stmt->execute($values);
        return $this->findById($id);
    }

    public function delete($id) {
        // Soft delete
        $stmt = $this->db->prepare("UPDATE order_details SET is_deleted = 1 WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>
