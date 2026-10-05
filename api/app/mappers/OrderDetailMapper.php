<?php
class OrderDetailMapper {
    /**
     * Chuyển đổi từ dữ liệu DB (mảng) sang DTO
     * @param array $row - Dữ liệu từ database
     * @return OrderDetailDTO
     */
    public static function toDTO($row) {
        $dto = new OrderDetailDTO();
        $dto->id                 = $row['id'] ?? null;
        $dto->order_id           = $row['order_id'] ?? null;
        $dto->product_variant_id = $row['product_variant_id'] ?? null;
        $dto->quantity           = $row['quantity'] ?? null;
        $dto->unit_price         = $row['unit_price'] ?? null;
        $dto->is_deleted         = $row['is_deleted'] ?? 0;
        return $dto;
    }

    /**
     * Chuyển đổi từ dữ liệu DB (mảng) sang Model
     * @param array $row - Dữ liệu từ database
     * @return OrderDetail
     */
    public static function toModel($row) {
        return new OrderDetail($row);
    }
}
?>