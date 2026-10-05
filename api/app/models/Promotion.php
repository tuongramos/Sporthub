<?php
class Promotion {
    private $id;
    private $code;
    private $discount_percent;
    private $discount_amount;
    private $min_order_value;
    private $start_date;
    private $end_date;
    private $status;
    private $is_deleted;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->code = $data['code'] ?? null;
            $this->discount_percent = $data['discount_percent'] ?? null;
            $this->discount_amount = $data['discount_amount'] ?? null;
            $this->min_order_value = $data['min_order_value'] ?? null;
            $this->start_date = $data['start_date'] ?? null;
            $this->end_date = $data['end_date'] ?? null;
            $this->status = $data['status'] ?? null;
            $this->is_deleted = $data['is_deleted'] ?? null;
        }
    }

    // Getters
    public function getId() { return $this->id; }
    public function getCode() { return $this->code; }
    public function getDiscountPercent() { return $this->discount_percent; }
    public function getDiscountAmount() { return $this->discount_amount; }
    public function getMinOrderValue() { return $this->min_order_value; }
    public function getStartDate() { return $this->start_date; }
    public function getEndDate() { return $this->end_date; }
    public function getStatus() { return $this->status; }
    public function getIsDeleted() { return $this->is_deleted; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setCode($code) { $this->code = $code; }
    public function setDiscountPercent($discount_percent) { $this->discount_percent = $discount_percent; }
    public function setDiscountAmount($discount_amount) { $this->discount_amount = $discount_amount; }
    public function setMinOrderValue($min_order_value) { $this->min_order_value = $min_order_value; }
    public function setStartDate($start_date) { $this->start_date = $start_date; }
    public function setEndDate($end_date) { $this->end_date = $end_date; }
    public function setStatus($status) { $this->status = $status; }
    public function setIsDeleted($is_deleted) { $this->is_deleted = $is_deleted; }
}
?>
