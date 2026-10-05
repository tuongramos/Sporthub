<?php
class Product {
    private $id;
    private $category_id;
    private $name;
    private $description;
    private $price;
    private $image_url;
    private $gallery;
    private $status;
    private $is_deleted;
    private $created_at;
    private $updated_at;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->category_id = $data['category_id'] ?? null;
            $this->name = $data['name'] ?? null;
            $this->description = $data['description'] ?? null;
            $this->price = $data['price'] ?? null;
            $this->image_url = $data['image_url'] ?? null;
            $this->gallery = $data['gallery'] ?? null;
            $this->status = $data['status'] ?? null;
            $this->is_deleted = $data['is_deleted'] ?? null;
            $this->created_at = $data['created_at'] ?? null;
            $this->updated_at = $data['updated_at'] ?? null;
        }
    }

    //Getters

    public function getId() {
        return $this->id;
    }

    public function getCategoryId() {
        return $this->category_id;
    }

    public function getName() {
        return $this->name;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getPrice() {
        return $this->price;
    }

    public function getImageUrl() {
        return $this->image_url;
    }

    public function getGallery() {
        return $this->gallery;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getIsDeleted() {
        return $this->is_deleted;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function getUpdatedAt() {
        return $this->updated_at;
    }

    //Setters

    public function setId($id) {
        $this->id = $id;
    }

    public function setCategoryId($category_id) {
        $this->category_id = $category_id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setPrice($price) {
        $this->price = $price;
    }

    public function setImageUrl($image_url) {
        $this->image_url = $image_url;
    }

    public function setGallery($gallery) {
        $this->gallery = $gallery;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function setIsDeleted($is_deleted) {
        $this->is_deleted = $is_deleted;
    }

    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }

    public function setUpdatedAt($updated_at) {
        $this->updated_at = $updated_at;
    }
}
?>
