<?php
class Database {
    private $conn;

    public function __construct() {
        $config = require __DIR__ . '/../../config/dbConf.php';
        try {
            $portStr = isset($config['port']) ? ";port=" . $config['port'] : "";
            $dsn = "mysql:host=" . $config['host'] . $portStr . ";dbname=" . $config['db_name'];
            $options = array(
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
            );
            $this->conn = new PDO($dsn, $config['username'], $config['password'], $options);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8mb4");
        } catch(PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => "Lỗi kết nối CSDL: " . $e->getMessage()]);
            exit;
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>
