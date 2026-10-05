<?php
class CategoryMapper {
    public static function toDTO($row) {
        $dto = new CategoryDTO();
        $dto->id = $row['id'] ?? null;
        $dto->name = $row['name'] ?? null;
        $dto->description = $row['description'] ?? null;
        $dto->image_url = $row['image_url'] ?? null;
        $dto->status = $row['status'] ?? null;
        $dto->created_at = $row['created_at'] ?? null;
        $dto->is_deleted = $row['is_deleted'] ?? null;
        return $dto;
    }
}
?>
