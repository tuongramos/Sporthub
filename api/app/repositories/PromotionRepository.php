<?php
class PromotionRepository {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function findAll() {
        $stmt = $this->db->query("SELECT * FROM promotions WHERE (is_deleted = 0 OR is_deleted IS NULL)");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $query = "SELECT * FROM promotions WHERE id = :id AND (is_deleted = 0 OR is_deleted IS NULL)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function search($keyword) {
        $query = "SELECT * FROM promotions WHERE (code LIKE :kw OR status LIKE :kw) AND (is_deleted = 0 OR is_deleted IS NULL) ORDER BY id DESC";
        $stmt = $this->db->prepare($query);
        $searchTerm = '%' . $keyword . '%';
        $stmt->bindParam(':kw', $searchTerm, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO promotions (code, discount_percent, discount_amount, min_order_value, start_date, end_date, status, is_deleted) 
                  VALUES (:code, :discount_percent, :discount_amount, :min_order_value, :start_date, :end_date, :status, :is_deleted)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':code', $data['code']);
        $stmt->bindParam(':discount_percent', $data['discount_percent']);
        $stmt->bindParam(':discount_amount', $data['discount_amount']);
        $stmt->bindParam(':min_order_value', $data['min_order_value']);
        $stmt->bindParam(':start_date', $data['start_date']);
        $stmt->bindParam(':end_date', $data['end_date']);
        $stmt->bindParam(':status', $data['status']);
        
        $isDeleted = isset($data['is_deleted']) ? $data['is_deleted'] : 0;
        $stmt->bindParam(':is_deleted', $isDeleted, PDO::PARAM_INT);
        $stmt->execute();

        $lastId = $this->db->lastInsertId();
        return $this->findById($lastId);
    }

    public function update($id, $data) {
        $query = "UPDATE promotions SET 
                    code = :code, 
                    discount_percent = :discount_percent, 
                    discount_amount = :discount_amount, 
                    min_order_value = :min_order_value, 
                    start_date = :start_date, 
                    end_date = :end_date, 
                    status = :status, 
                    is_deleted = :is_deleted 
                  WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':code', $data['code']);
        $stmt->bindParam(':discount_percent', $data['discount_percent']);
        $stmt->bindParam(':discount_amount', $data['discount_amount']);
        $stmt->bindParam(':min_order_value', $data['min_order_value']);
        $stmt->bindParam(':start_date', $data['start_date']);
        $stmt->bindParam(':end_date', $data['end_date']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':is_deleted', $data['is_deleted'], PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $this->findById($id);
    }

    public function delete($id) {
        $query = "UPDATE promotions SET is_deleted = 1 WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>
