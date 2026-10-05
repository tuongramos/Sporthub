<?php
class OrderDetail {
    private $id;
    private $order_id;
    private $product_variant_id;
    private $quantity;
    private $unit_price;
    private $is_deleted;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id                 = $data['id'] ?? null;
            $this->order_id           = $data['order_id'] ?? null;
            $this->product_variant_id = $data['product_variant_id'] ?? null;
            $this->quantity           = $data['quantity'] ?? null;
            $this->unit_price         = $data['unit_price'] ?? null;
            $this->is_deleted         = $data['is_deleted'] ?? 0;
        }
    }

    // Getters
    public function getId()               { return $this->id; }
    public function getOrderId()          { return $this->order_id; }
    public function getProductVariantId() { return $this->product_variant_id; }
    public function getQuantity()         { return $this->quantity; }
    public function getUnitPrice()        { return $this->unit_price; }
    public function getIsDeleted()        { return $this->is_deleted; }

    // Setters
    public function setId($id)                                 { $this->id = $id; }
    public function setOrderId($order_id)                      { $this->order_id = $order_id; }
    public function setProductVariantId($product_variant_id)   { $this->product_variant_id = $product_variant_id; }
    public function setQuantity($quantity)                     { $this->quantity = $quantity; }
    public function setUnitPrice($unit_price)                  { $this->unit_price = $unit_price; }
    public function setIsDeleted($is_deleted)                  { $this->is_deleted = $is_deleted; }
}
?>