<?php
class Category {
    private $id;
    private $name;
    private $description;
    private $image_url;
    private $status;
    private $created_at;
    private $is_deleted;

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->image_url = $data['image_url'] ?? null;
        $this->status = $data['status'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->is_deleted = $data['is_deleted'] ?? null;
    }

    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getDescription() { return $this->description; }
    public function getImageUrl() { return $this->image_url; }
    public function getStatus() { return $this->status; }
    public function getCreatedAt() { return $this->created_at; }
    public function getIsDeleted() { return $this->is_deleted; }
}
?>
