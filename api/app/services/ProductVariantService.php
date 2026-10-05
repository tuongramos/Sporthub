<?php
class ProductVariantService {
    private $repository;

    public function __construct() {
        $this->repository = new ProductVariantRepository();
    }

    public function getAll() {
        $rows = $this->repository->findAll();
        return array_map(function ($row) {
            return ProductVariantMapper::toDTO($row);
        }, $rows);
    }

    public function getByProductId($productId) {
        $rows = $this->repository->findByProductId($productId);
        return array_map(function ($row) {
            return ProductVariantMapper::toDTO($row);
        }, $rows);
    }

    public function getById($id) {
        $row = $this->repository->findById($id);
        if (!$row) return null;
        return ProductVariantMapper::toDTO($row);
    }

    public function create($data) {
        if (empty($data['product_id'])) {
            return ['success' => false, 'message' => 'Product ID là bắt buộc'];
        }
        if (empty($data['size'])) {
            return ['success' => false, 'message' => 'Size là bắt buộc'];
        }
        
        $data['color'] = $data['color'] ?? '';
        $data['stock_quantity'] = $data['stock_quantity'] ?? 0;

        $id = $this->repository->create($data);
        if ($id) {
            return ['success' => true, 'message' => 'Thêm biến thể thành công', 'id' => $id];
        }
        return ['success' => false, 'message' => 'Thêm biến thể thất bại'];
    }

    public function update($id, $data) {
        $existing = $this->repository->findById($id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Biến thể không tồn tại'];
        }

        $data['product_id'] = $data['product_id'] ?? $existing['product_id'];
        $data['size'] = $data['size'] ?? $existing['size'];
        $data['color'] = $data['color'] ?? $existing['color'];
        $data['stock_quantity'] = $data['stock_quantity'] ?? $existing['stock_quantity'];

        $result = $this->repository->update($id, $data);
        if ($result) {
            return ['success' => true, 'message' => 'Cập nhật biến thể thành công'];
        }
        return ['success' => false, 'message' => 'Cập nhật biến thể thất bại'];
    }

    public function delete($id) {
        $existing = $this->repository->findById($id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Biến thể không tồn tại'];
        }

        $result = $this->repository->delete($id);
        if ($result) {
            return ['success' => true, 'message' => 'Xóa biến thể thành công'];
        }
        return ['success' => false, 'message' => 'Xóa biến thể thất bại'];
    }
}
?>
