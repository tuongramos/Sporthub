<?php
class ProductService {
    private $repository;

    public function __construct() {
        $this->repository = new ProductRepository();
    }

    /**
     * Lấy tất cả sản phẩm và chuyển sang DTO
     * @return array
     */
    public function getAll() {
        $rows = $this->repository->findAll();
        return array_map(function ($row) {
            return ProductMapper::toDTO($row);
        }, $rows);
    }

    /**
     * Lấy 1 sản phẩm theo ID và chuyển sang DTO
     * @param int $id
     * @return ProductDTO|null
     */
    public function getById($id) {
        $row = $this->repository->findById($id);
        if (!$row) {
            return null;
        }
        return ProductMapper::toDTO($row);
    }

    /**
     * Lấy sản phẩm theo danh mục và chuyển sang DTO
     * @param int $categoryId
     * @return array
     */
    public function getByCategory($categoryId) {
        $rows = $this->repository->findByCategory($categoryId);
        return array_map(function ($row) {
            return ProductMapper::toDTO($row);
        }, $rows);
    }

    /**
     * Tìm kiếm sản phẩm theo từ khóa và chuyển sang DTO
     * @param string $keyword
     * @return array
     */
    public function search($keyword) {
        require_once __DIR__ . '/../core/vietnamese_helper.php';
        $keywordNoTone = strtolower(removeVietnameseTone($keyword));
        $keywordLower = strtolower($keyword);
        
        $allRows = $this->repository->findAll();
        $rows = array_filter($allRows, function($row) use ($keywordNoTone, $keywordLower) {
            $name = strtolower(removeVietnameseTone($row['name'] ?? ''));
            $desc = strtolower(removeVietnameseTone($row['description'] ?? ''));
            $catName = strtolower(removeVietnameseTone($row['category_name'] ?? ''));
            
            return strpos($name, $keywordNoTone) !== false || 
                   strpos($desc, $keywordNoTone) !== false || 
                   strpos($catName, $keywordNoTone) !== false;
        });
        
        // Re-index array after filter
        $rows = array_values($rows);

        return array_map(function ($row) {
            return ProductMapper::toDTO($row);
        }, $rows);
    }

    /**
     * Tạo sản phẩm mới
     * Validate các trường bắt buộc và tự động tính discount nếu có sale_price
     * @param array $data
     * @return array - kết quả ['success' => bool, 'message' => string, 'id' => int|null]
     */
    public function create($data) {
        if (empty($data['name'])) {
            return ['success' => false, 'message' => 'Tên sản phẩm là bắt buộc'];
        }
        if (empty($data['category_id'])) {
            return ['success' => false, 'message' => 'Danh mục sản phẩm là bắt buộc'];
        }
        if (!isset($data['price']) || $data['price'] <= 0) {
            return ['success' => false, 'message' => 'Giá sản phẩm là bắt buộc và phải lớn hơn 0'];
        }

        $data['description'] = $data['description'] ?? null;
        $data['image_url'] = $data['image_url'] ?? null;
        $data['gallery'] = $data['gallery'] ?? null;
        $data['status'] = $data['status'] ?? 'active';

        $id = $this->repository->create($data);
        if ($id) {
            return ['success' => true, 'message' => 'Thêm sản phẩm thành công', 'id' => $id];
        }
        return ['success' => false, 'message' => 'Thêm sản phẩm thất bại'];
    }

    /**
     * Cập nhật sản phẩm
     * Validate và tự động tính discount nếu có sale_price
     * @param int $id
     * @param array $data
     * @return array
     */
    public function update($id, $data) {
        $existing = $this->repository->findById($id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Sản phẩm không tồn tại'];
        }

        if (empty($data['name'])) {
            return ['success' => false, 'message' => 'Tên sản phẩm là bắt buộc'];
        }
        if (empty($data['category_id'])) {
            return ['success' => false, 'message' => 'Danh mục sản phẩm là bắt buộc'];
        }
        if (!isset($data['price']) || $data['price'] <= 0) {
            return ['success' => false, 'message' => 'Giá sản phẩm là bắt buộc và phải lớn hơn 0'];
        }

        $data['description'] = $data['description'] ?? $existing['description'];
        $data['image_url'] = $data['image_url'] ?? $existing['image_url'];
        $data['gallery'] = $data['gallery'] ?? $existing['gallery'];
        $data['status'] = $data['status'] ?? $existing['status'];

        $result = $this->repository->update($id, $data);
        if ($result) {
            return ['success' => true, 'message' => 'Cập nhật sản phẩm thành công'];
        }
        return ['success' => false, 'message' => 'Cập nhật sản phẩm thất bại'];
    }

    /**
     * Xóa sản phẩm theo ID
     * @param int $id
     * @return array
     */
    public function delete($id) {
        $existing = $this->repository->findById($id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Sản phẩm không tồn tại'];
        }

        $result = $this->repository->delete($id);
        if ($result) {
            return ['success' => true, 'message' => 'Xóa sản phẩm thành công'];
        }
        return ['success' => false, 'message' => 'Xóa sản phẩm thất bại'];
    }


}
?>
