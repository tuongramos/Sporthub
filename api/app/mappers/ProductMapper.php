<?php
class ProductMapper {
    /**
     * Chuyển đổi dữ liệu từ DB row (mảng kết hợp) sang ProductDTO
     * @param array $row - Dòng dữ liệu từ PDO fetch
     * @return ProductDTO
     */
    public static function toDTO($row) {
        $dto = new ProductDTO();
        $dto->id = $row['id'] ?? null;
        $dto->category_id = $row['category_id'] ?? null;
        $dto->category_name = $row['category_name'] ?? null;
        $dto->name = $row['name'] ?? null;
        $dto->description = $row['description'] ?? null;
        $dto->price = $row['price'] ?? null;
        $dto->image_url = $row['image_url'] ?? null;
        $dto->gallery = isset($row['gallery']) ? json_decode($row['gallery'], true) : [];
        $dto->status = $row['status'] ?? null;
        $dto->created_at = $row['created_at'] ?? null;
        return $dto;
    }
}
?>
