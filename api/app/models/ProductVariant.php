<?php
class ProductVariant {
    private $id;
    private $product_id;
    private $size;
    private $color;
    private $stock_quantity;
    private $created_at;
    private $is_deleted;

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getProductId() { return $this->product_id; }
    public function setProductId($product_id) { $this->product_id = $product_id; }

    public function getSize() { return $this->size; }
    public function setSize($size) { $this->size = $size; }

    public function getColor() { return $this->color; }
    public function setColor($color) { $this->color = $color; }

    public function getStockQuantity() { return $this->stock_quantity; }
    public function setStockQuantity($stock_quantity) { $this->stock_quantity = $stock_quantity; }

    public function getCreatedAt() { return $this->created_at; }
    public function setCreatedAt($created_at) { $this->created_at = $created_at; }

    public function getIsDeleted() { return $this->is_deleted; }
    public function setIsDeleted($is_deleted) { $this->is_deleted = $is_deleted; }
}
?>
