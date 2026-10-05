<?php

class UserRepository {

    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function findAll() {
        $stmt = $this->db->query("SELECT * FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ? AND (is_deleted = 0 OR is_deleted IS NULL)");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByUsername($username) {
        $query = "SELECT * FROM users WHERE username = :username AND (is_deleted = 0 OR is_deleted IS NULL) LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Tìm kiếm người dùng theo từ khóa
     * @param string $keyword
     * @return array
     */
    public function search($keyword) {
        $stmt = $this->db->prepare(
            "SELECT * FROM users 
             WHERE (username LIKE :kw OR email LIKE :kw OR first_name LIKE :kw OR last_name LIKE :kw OR phone_number LIKE :kw) 
             AND (is_deleted = 0 OR is_deleted IS NULL) 
             ORDER BY created_at DESC"
        );
        $searchTerm = '%' . $keyword . '%';
        $stmt->bindParam(':kw', $searchTerm, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($username, $email, $password, $first_name, $last_name, $phone_number, $address, $sex, $role = 'USER') {
        $stmt = $this->db->prepare(
            "INSERT INTO users (username, email, password, first_name, last_name, phone_number, address, sex, role, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')"
        );

        return $stmt->execute([
            $username,
            $email,
            $password,
            $first_name,
            $last_name,
            $phone_number,
            $address,
            $sex,
            $role
        ]);
    }

    public function update($id, $username, $email, $password, $first_name, $last_name, $phone_number, $address, $sex, $role, $status) {
        $stmt = $this->db->prepare(
            "UPDATE users
             SET username = ?, email = ?, password = ?, first_name = ?, last_name = ?, phone_number = ?, address = ?, sex = ?, role = ?, status = ?
             WHERE id = ?"
        );

        return $stmt->execute([
            $username,
            $email,
            $password,
            $first_name,
            $last_name,
            $phone_number,
            $address,
            $sex,
            $role,
            $status,
            $id
        ]);
    }

    public function delete($id) {
        // Soft delete
        $stmt = $this->db->prepare("UPDATE users SET is_deleted = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>