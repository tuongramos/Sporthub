<?php
class OrderRepository {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function findAll() {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE (is_deleted = 0 OR is_deleted IS NULL) ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = :id AND (is_deleted = 0 OR is_deleted IS NULL)");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByStatus($status) {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE status = :status AND (is_deleted = 0 OR is_deleted IS NULL) ORDER BY id DESC");
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByUserId($user_id) {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE user_id = :user_id AND (is_deleted = 0 OR is_deleted IS NULL) ORDER BY id DESC");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO orders (order_date, total_amount, final_amount, shipping_address, status, user_id, promotion_id, is_deleted)
            VALUES (:order_date, :total_amount, :final_amount, :shipping_address, :status, :user_id, :promotion_id, 0)
        ");
        $stmt->bindParam(':order_date',       $data['order_date']);
        $stmt->bindParam(':total_amount',     $data['total_amount']);
        $stmt->bindParam(':final_amount',     $data['final_amount']);
        $stmt->bindParam(':shipping_address', $data['shipping_address']);
        $stmt->bindParam(':status',           $data['status']);
        $stmt->bindParam(':user_id',          $data['user_id']);
        $stmt->bindParam(':promotion_id',     $data['promotion_id']);
        $stmt->execute();

        $lastId = $this->db->lastInsertId();
        return $this->findById($lastId);
    }

    public function update($id, $data) {
        $fields = [];
        $values = [];
        $allowedColumns = ['order_date', 'total_amount', 'final_amount', 'shipping_address', 'status', 'user_id', 'promotion_id', 'is_deleted'];

        foreach ($allowedColumns as $col) {
            if (array_key_exists($col, $data)) {
                $fields[] = "$col = :$col";
                $values[":$col"] = $data[$col];
            }
        }
        if (empty($fields)) return $this->findById($id);

        $values[':id'] = $id;
        $stmt = $this->db->prepare("UPDATE orders SET " . implode(', ', $fields) . " WHERE id = :id");
        $stmt->execute($values);
        return $this->findById($id);
    }

    public function delete($id) {
        // Soft delete
        $stmt = $this->db->prepare("UPDATE orders SET is_deleted = 1 WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>