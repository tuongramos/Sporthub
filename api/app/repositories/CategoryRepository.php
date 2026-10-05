<?php
class CategoryRepository {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function findAll() {
        $query = "SELECT * FROM categories WHERE (is_deleted = 0 OR is_deleted IS NULL) ORDER BY id ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $query = "SELECT * FROM categories WHERE id = :id AND (is_deleted = 0 OR is_deleted IS NULL)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO categories (name, description, image_url, status, is_deleted) 
                  VALUES (:name, :description, :image_url, :status, 0)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
        $stmt->bindParam(':image_url', $data['image_url'], PDO::PARAM_STR);
        $stmt->bindParam(':status', $data['status'], PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function update($id, $data) {
        $query = "UPDATE categories 
                  SET name = :name, 
                      description = :description, 
                      image_url = :image_url, 
                      status = :status 
                  WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
        $stmt->bindParam(':image_url', $data['image_url'], PDO::PARAM_STR);
        $stmt->bindParam(':status', $data['status'], PDO::PARAM_STR);
        
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "UPDATE categories SET is_deleted = 1 WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
