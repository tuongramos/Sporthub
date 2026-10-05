<?php
class PaymentRepository {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function findAll() {
        $stmt = $this->db->prepare("SELECT * FROM payments WHERE (is_deleted = 0 OR is_deleted IS NULL) ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM payments WHERE id = :id AND (is_deleted = 0 OR is_deleted IS NULL)");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByOrderId($orderId) {
        $stmt = $this->db->prepare("SELECT * FROM payments WHERE order_id = :order_id AND (is_deleted = 0 OR is_deleted IS NULL)");
        $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO payments (amount, payment_method, payment_date, status, transaction_id, order_id, is_deleted)
            VALUES (:amount, :payment_method, :payment_date, :status, :transaction_id, :order_id, 0)
        ");
        $stmt->bindParam(':amount',         $data['amount']);
        $stmt->bindParam(':payment_method', $data['payment_method']);
        $stmt->bindParam(':payment_date',   $data['payment_date']);
        $stmt->bindParam(':status',         $data['status']);
        $stmt->bindParam(':transaction_id', $data['transaction_id']);
        $stmt->bindParam(':order_id',       $data['order_id']);
        $stmt->execute();

        $lastId = $this->db->lastInsertId();
        return $this->findById($lastId);
    }

    public function update($id, $data) {
        $fields = [];
        $values = [];
        $allowedColumns = ['amount', 'payment_method', 'payment_date', 'status', 'transaction_id', 'order_id', 'is_deleted'];

        foreach ($allowedColumns as $col) {
            if (array_key_exists($col, $data)) {
                $fields[] = "$col = :$col";
                $values[":$col"] = $data[$col];
            }
        }
        if (empty($fields)) return $this->findById($id);

        $values[':id'] = $id;
        $stmt = $this->db->prepare("UPDATE payments SET " . implode(', ', $fields) . " WHERE id = :id");
        $stmt->execute($values);
        return $this->findById($id);
    }

    public function delete($id) {
        // Soft delete
        $stmt = $this->db->prepare("UPDATE payments SET is_deleted = 1 WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>