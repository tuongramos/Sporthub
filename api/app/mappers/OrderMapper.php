<?php
class OrderMapper {

    /**
     * Chuyển đổi từ dữ liệu DB (mảng) sang DTO
     * @param array $row - Dữ liệu từ database
     * @return OrderDTO
     */
    public static function toDTO($row) {
        $dto = new OrderDTO();
        $dto->id           = $row['id'] ?? null;
        $dto->order_date   = $row['order_date'] ?? null;
        $dto->total_amount = $row['total_amount'] ?? null;
        $dto->final_amount = $row['final_amount'] ?? null;
        $dto->shipping_address = $row['shipping_address'] ?? null;
        $dto->status       = $row['status'] ?? null;
        $dto->user_id      = $row['user_id'] ?? null;
        $dto->promotion_id = $row['promotion_id'] ?? null;
        $dto->is_deleted   = $row['is_deleted'] ?? 0;
        return $dto;
    }

    /**
     * Chuyển đổi từ dữ liệu DB (mảng) sang Model
     * @param array $row - Dữ liệu từ database
     * @return Order
     */
    public static function toModel($row) {
        return new Order($row);
    }
}
?>