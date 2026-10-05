<?php
class PromotionService {
    private $repository;

    public function __construct() {
        $this->repository = new PromotionRepository();
    }

    public function getAll() {
        $rows = $this->repository->findAll();
        $dtos = [];
        foreach ($rows as $row) {
            $dtos[] = PromotionMapper::toDTO($row);
        }
        return $dtos;
    }

    public function getById($id) {
        $row = $this->repository->findById($id);
        if (!$row) {
            return null;
        }
        return PromotionMapper::toDTO($row);
    }

    public function search($keyword) {
        $rows = $this->repository->search($keyword);
        $dtos = [];
        foreach ($rows as $row) {
            $dtos[] = PromotionMapper::toDTO($row);
        }
        return $dtos;
    }

    public function create($data) {
        if (empty($data['code'])) {
            throw new Exception('Mã khuyến mãi không được để trống');
        }

        $promotionData = [
            'code' => trim($data['code']),
            'discount_percent' => isset($data['discount_percent']) ? floatval($data['discount_percent']) : null,
            'discount_amount' => isset($data['discount_amount']) ? floatval($data['discount_amount']) : null,
            'min_order_value' => isset($data['min_order_value']) ? floatval($data['min_order_value']) : null,
            'start_date' => isset($data['start_date']) ? $data['start_date'] : null,
            'end_date' => isset($data['end_date']) ? $data['end_date'] : null,
            'status' => isset($data['status']) ? trim($data['status']) : 'active',
            'is_deleted' => isset($data['is_deleted']) ? (int)$data['is_deleted'] : 0
        ];

        $row = $this->repository->create($promotionData);
        return PromotionMapper::toDTO($row);
    }

    public function update($id, $data) {
        $existing = $this->repository->findById($id);
        if (!$existing) {
            return null;
        }

        if (isset($data['code']) && empty(trim($data['code']))) {
            throw new Exception('Mã khuyến mãi không được để trống');
        }

        $promotionData = [
            'code' => isset($data['code']) ? trim($data['code']) : $existing['code'],
            'discount_percent' => isset($data['discount_percent']) ? floatval($data['discount_percent']) : $existing['discount_percent'],
            'discount_amount' => isset($data['discount_amount']) ? floatval($data['discount_amount']) : $existing['discount_amount'],
            'min_order_value' => isset($data['min_order_value']) ? floatval($data['min_order_value']) : $existing['min_order_value'],
            'start_date' => isset($data['start_date']) ? $data['start_date'] : $existing['start_date'],
            'end_date' => isset($data['end_date']) ? $data['end_date'] : $existing['end_date'],
            'status' => isset($data['status']) ? trim($data['status']) : $existing['status'],
            'is_deleted' => isset($data['is_deleted']) ? (int)$data['is_deleted'] : $existing['is_deleted']
        ];

        $row = $this->repository->update($id, $promotionData);
        return PromotionMapper::toDTO($row);
    }

    public function delete($id) {
        $existing = $this->repository->findById($id);
        if (!$existing) {
            return false;
        }

        return $this->repository->delete($id);
    }
}
?>
