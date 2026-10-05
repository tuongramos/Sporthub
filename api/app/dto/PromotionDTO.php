<?php
class PromotionDTO {
    public $id;
    public $code;
    public $discount_percent;
    public $discount_amount;
    public $min_order_value;
    public $start_date;
    public $end_date;
    public $status;
    public $is_deleted;

    public function __construct($id = null, $code = null, $discount_percent = null, $discount_amount = null, $min_order_value = null, $start_date = null, $end_date = null, $status = null, $is_deleted = null) {
        $this->id = $id;
        $this->code = $code;
        $this->discount_percent = $discount_percent;
        $this->discount_amount = $discount_amount;
        $this->min_order_value = $min_order_value;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->status = $status;
        $this->is_deleted = $is_deleted;
    }
}
?>
