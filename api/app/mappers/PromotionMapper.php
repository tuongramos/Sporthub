<?php
class PromotionMapper {
    public static function toDTO($row) {
        if ($row instanceof Promotion) {
            return new PromotionDTO(
                $row->getId(),
                $row->getCode(),
                $row->getDiscountPercent(),
                $row->getDiscountAmount(),
                $row->getMinOrderValue(),
                $row->getStartDate(),
                $row->getEndDate(),
                $row->getStatus(),
                $row->getIsDeleted()
            );
        }

        $dto = new PromotionDTO();
        $dto->id = $row['id'] ?? null;
        $dto->code = $row['code'] ?? null;
        $dto->discount_percent = $row['discount_percent'] ?? null;
        $dto->discount_amount = $row['discount_amount'] ?? null;
        $dto->min_order_value = $row['min_order_value'] ?? null;
        $dto->start_date = $row['start_date'] ?? null;
        $dto->end_date = $row['end_date'] ?? null;
        $dto->status = $row['status'] ?? null;
        $dto->is_deleted = $row['is_deleted'] ?? null;
        return $dto;
    }

    public static function toModel($row) {
        return new Promotion($row);
    }
}
?>
