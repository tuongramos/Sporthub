<?php
class CategoryService {
    private $repository;

    public function __construct() {
        $this->repository = new CategoryRepository();
    }

    public function getAll() {
        $rows = $this->repository->findAll();
        $dtos = [];
        foreach ($rows as $row) {
            $dtos[] = CategoryMapper::toDTO($row);
        }
        return $dtos;
    }

    public function getById($id) {
        $row = $this->repository->findById($id);
        if (!$row) return null;
        return CategoryMapper::toDTO($row);
    }

    public function create($data) {
        if (empty($data['name'])) {
            return ['success' => false, 'message' => 'Tên danh mục không được để trống'];
        }

        $categoryData = [
            'name' => trim($data['name']),
            'description' => $data['description'] ?? null,
            'image_url' => $data['image_url'] ?? null,
            'status' => $data['status'] ?? 'active'
        ];

        $id = $this->repository->create($categoryData);
        if ($id) {
            return ['success' => true, 'message' => 'Tạo danh mục thành công', 'id' => $id];
        }
        return ['success' => false, 'message' => 'Tạo danh mục thất bại'];
    }

    public function update($id, $data) {
        $existing = $this->repository->findById($id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Danh mục không tồn tại'];
        }

        if (empty($data['name'])) {
            return ['success' => false, 'message' => 'Tên danh mục không được để trống'];
        }

        $categoryData = [
            'name' => trim($data['name']),
            'description' => $data['description'] ?? $existing['description'],
            'image_url' => $data['image_url'] ?? $existing['image_url'],
            'status' => $data['status'] ?? $existing['status']
        ];

        $result = $this->repository->update($id, $categoryData);
        if ($result) {
            return ['success' => true, 'message' => 'Cập nhật danh mục thành công'];
        }
        return ['success' => false, 'message' => 'Cập nhật danh mục thất bại'];
    }

    public function delete($id) {
        $existing = $this->repository->findById($id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Danh mục không tồn tại'];
        }

        $result = $this->repository->delete($id);
        if ($result) {
            return ['success' => true, 'message' => 'Xóa danh mục thành công'];
        }
        return ['success' => false, 'message' => 'Xóa danh mục thất bại'];
    }
}
?>
