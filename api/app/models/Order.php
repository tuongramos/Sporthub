<?php
class Order {
    private $id;
    private $order_date;
    private $total_amount;
    private $final_amount;
    private $shipping_address;
    private $status;
    private $user_id;
    private $promotion_id;
    private $is_deleted;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id               = $data['id'] ?? null;
            $this->order_date       = $data['order_date'] ?? null;
            $this->total_amount     = $data['total_amount'] ?? null;
            $this->final_amount     = $data['final_amount'] ?? null;
            $this->shipping_address = $data['shipping_address'] ?? null;
            $this->status           = $data['status'] ?? null;
            $this->user_id          = $data['user_id'] ?? null;
            $this->promotion_id     = $data['promotion_id'] ?? null;
            $this->is_deleted       = $data['is_deleted'] ?? 0;
        }
    }

    // Getters
    public function getId()              { return $this->id; }
    public function getOrderDate()       { return $this->order_date; }
    public function getTotalAmount()     { return $this->total_amount; }
    public function getFinalAmount()     { return $this->final_amount; }
    public function getShippingAddress() { return $this->shipping_address; }
    public function getStatus()          { return $this->status; }
    public function getUserId()          { return $this->user_id; }
    public function getPromotionId()     { return $this->promotion_id; }
    public function getIsDeleted()       { return $this->is_deleted; }

    // Setters
    public function setId($id)                           { $this->id = $id; }
    public function setOrderDate($order_date)            { $this->order_date = $order_date; }
    public function setTotalAmount($total_amount)        { $this->total_amount = $total_amount; }
    public function setFinalAmount($final_amount)        { $this->final_amount = $final_amount; }
    public function setShippingAddress($shipping_address){ $this->shipping_address = $shipping_address; }
    public function setStatus($status)                   { $this->status = $status; }
    public function setUserId($user_id)                  { $this->user_id = $user_id; }
    public function setPromotionId($promotion_id)        { $this->promotion_id = $promotion_id; }
    public function setIsDeleted($is_deleted)            { $this->is_deleted = $is_deleted; }
}
?>
