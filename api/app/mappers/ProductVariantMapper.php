<?php
class ProductVariantMapper {
    /**
     * @param array $row
     * @return ProductVariantDTO|null
     */
    public static function toDTO($row) {
        if (!$row) return null;

        $dto = new ProductVariantDTO();
        $dto->id = $row['id'] ?? null;
        $dto->product_id = $row['product_id'] ?? null;
        $dto->size = $row['size'] ?? null;
        $dto->color = $row['color'] ?? null;
        $dto->stock_quantity = $row['stock_quantity'] ?? null;
        $dto->created_at = $row['created_at'] ?? null;
        
        return $dto;
    }
}
?>
