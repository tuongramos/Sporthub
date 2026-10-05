<?php
class PaymentMapper {
    public static function toDTO($row) {
        $dto = new PaymentDTO();
        $dto->id             = $row['id'] ?? null;
        $dto->amount         = $row['amount'] ?? null;
        $dto->payment_method = $row['payment_method'] ?? null;
        $dto->payment_date   = $row['payment_date'] ?? null;
        $dto->status         = $row['status'] ?? null;
        $dto->transaction_id = $row['transaction_id'] ?? null;
        $dto->order_id       = $row['order_id'] ?? null;
        $dto->is_deleted     = $row['is_deleted'] ?? 0;
        return $dto;
    }

    public static function toModel($row) {
        return new Payment($row);
    }
}
?>
