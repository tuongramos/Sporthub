<?php
class OrderDetailService {
    private $repository;

    public function __construct() {
        $this->repository = new OrderDetailRepository();
    }

    public function getAll() {
        $rows = $this->repository->findAll();
        $dtos = [];
        foreach ($rows as $row) {
            $dtos[] = OrderDetailMapper::toDTO($row);
        }
        return $dtos;
    }

    public function getById($id) {
        $row = $this->repository->findById($id);
        if (!$row) {
            return null;
        }
        return OrderDetailMapper::toDTO($row);
    }

    public function create($data) {
        if (empty($data['order_id'])) {
            throw new Exception('Order ID không được để trống');
        }
        if (empty($data['product_variant_id'])) {
            throw new Exception('Product Variant ID không được để trống');
        }
        if (empty($data['quantity']) || $data['quantity'] <= 0) {
            throw new Exception('Số lượng phải lớn hơn 0');
        }
        if (!isset($data['unit_price'])) {
            throw new Exception('Đơn giá không được để trống');
        }

        $orderDetailData = [
            'order_id'           => intval($data['order_id']),
            'product_variant_id' => intval($data['product_variant_id']),
            'quantity'           => intval($data['quantity']),
            'unit_price'         => floatval($data['unit_price'])
        ];

        $row = $this->repository->create($orderDetailData);
        return OrderDetailMapper::toDTO($row);
    }

    public function update($id, $data) {
        $existing = $this->repository->findById($id);
        if (!$existing) {
            return null;
        }

        $updateData = [];
        if (isset($data['order_id']))           $updateData['order_id']           = intval($data['order_id']);
        if (isset($data['product_variant_id'])) $updateData['product_variant_id'] = intval($data['product_variant_id']);
        if (isset($data['quantity']))           $updateData['quantity']           = intval($data['quantity']);
        if (isset($data['unit_price']))         $updateData['unit_price']         = floatval($data['unit_price']);
        if (isset($data['is_deleted']))         $updateData['is_deleted']         = intval($data['is_deleted']);

        $row = $this->repository->update($id, $updateData);
        return OrderDetailMapper::toDTO($row);
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