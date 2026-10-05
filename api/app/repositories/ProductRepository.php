<?php
class ProductRepository {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Lấy tất cả sản phẩm kèm tên danh mục, sắp xếp mới nhất trước
     * @return array
     */
    public function findAll($categoryId = null) {
        if ($categoryId) {
            $stmt = $this->db->prepare("SELECT p.*, c.name as category_name 
                                      FROM products p 
                                      LEFT JOIN categories c ON p.category_id = c.id 
                                      WHERE p.category_id = ?");
            $stmt->execute([$categoryId]);
        } else {
            $stmt = $this->db->query("SELECT p.*, c.name as category_name 
                                    FROM products p 
                                    LEFT JOIN categories c ON p.category_id = c.id");
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy 1 sản phẩm theo ID kèm tên danh mục
     * @param int $id
     * @return array|false
     */
    public function findById($id) {
        $query = "SELECT p.*, c.name AS category_name 
                  FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.id = :id AND (p.is_deleted = 0 OR p.is_deleted IS NULL)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách sản phẩm theo danh mục
     * @param int $categoryId
     * @return array
     */
    public function findByCategory($categoryId) {
        $query = "SELECT p.*, c.name AS category_name 
                  FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.category_id = :category_id AND (p.is_deleted = 0 OR p.is_deleted IS NULL)
                  ORDER BY p.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tìm kiếm sản phẩm theo tên
     * @param string $keyword
     * @return array
     */
    public function search($keyword) {
        $query = "SELECT p.*, c.name AS category_name 
                  FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE (p.name LIKE :keyword OR p.description LIKE :keyword OR c.name LIKE :keyword) AND (p.is_deleted = 0 OR p.is_deleted IS NULL)
                  ORDER BY p.created_at DESC";
        $stmt = $this->db->prepare($query);
        $searchTerm = '%' . $keyword . '%';
        $stmt->bindParam(':keyword', $searchTerm, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Thêm sản phẩm mới
     * @param array $data
     * @return bool
     */
    public function create($data) {
        $query = "INSERT INTO products (category_id, name, description, price, image_url, gallery, status, is_deleted) 
                  VALUES (:category_id, :name, :description, :price, :image_url, :gallery, :status, 0)";
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':category_id', $data['category_id'], PDO::PARAM_INT);
        $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
        $stmt->bindParam(':price', $data['price'], PDO::PARAM_STR);
        $stmt->bindParam(':image_url', $data['image_url'], PDO::PARAM_STR);
        $stmt->bindParam(':gallery', $data['gallery'], PDO::PARAM_STR);
        $stmt->bindParam(':status', $data['status'], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Cập nhật sản phẩm theo ID
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $query = "UPDATE products 
                  SET category_id = :category_id, 
                      name = :name, 
                      description = :description, 
                      price = :price, 
                      image_url = :image_url, 
                      gallery = :gallery, 
                      status = :status 
                  WHERE id = :id";
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $data['category_id'], PDO::PARAM_INT);
        $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
        $stmt->bindParam(':price', $data['price'], PDO::PARAM_STR);
        $stmt->bindParam(':image_url', $data['image_url'], PDO::PARAM_STR);
        $stmt->bindParam(':gallery', $data['gallery'], PDO::PARAM_STR);
        $stmt->bindParam(':status', $data['status'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Xóa sản phẩm theo ID
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $query = "UPDATE products SET is_deleted = 1 WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Đếm tổng số sản phẩm (dùng cho phân trang sau này)
     * @return int
     */
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM products WHERE (is_deleted = 0 OR is_deleted IS NULL)";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $row['total'];
    }
}
?>
