<?php
class OrderService {
    private $repository;

    public function __construct() {
        $this->repository = new OrderRepository();
    }

    /**
     * Lấy tất cả đơn hàng
     * @return array - Mảng các OrderDTO
     */
    public function getAll() {
        $rows = $this->repository->findAll();
        $dtos = [];
        foreach ($rows as $row) {
            $dtos[] = OrderMapper::toDTO($row);
        }
        return $dtos;
    }

    /**
     * Lấy đơn hàng theo ID
     * @param int $id
     * @return OrderDTO|null
     */
    public function getById($id) {
        $row = $this->repository->findById($id);
        if (!$row) {
            return null;
        }
        return OrderMapper::toDTO($row);
    }

    /**
     * Tạo mới đơn hàng
     * @param array $data - Dữ liệu từ request
     * @return OrderDTO
     * @throws Exception
     */
    public function create($data) {
        if (empty($data['shipping_address'])) {
            throw new Exception('Địa chỉ giao hàng không được để trống');
        }
        if (!isset($data['total_amount']) || $data['total_amount'] < 0) {
            throw new Exception('Tổng tiền đơn hàng không hợp lệ');
        }

        $orderData = [
            'order_date'       => isset($data['order_date']) ? $data['order_date'] : date('Y-m-d H:i:s'),
            'total_amount'     => floatval($data['total_amount']),
            'final_amount'     => isset($data['final_amount']) ? floatval($data['final_amount']) : floatval($data['total_amount']),
            'shipping_address' => trim($data['shipping_address']),
            'status'           => isset($data['status']) ? trim($data['status']) : 'PENDING',
            'user_id'          => isset($data['user_id']) ? intval($data['user_id']) : null,
            'promotion_id'     => isset($data['promotion_id']) ? intval($data['promotion_id']) : null,
        ];

        $row = $this->repository->create($orderData);
        return OrderMapper::toDTO($row);
    }

    /**
     * Cập nhật đơn hàng theo ID
     * @param int $id
     * @param array $data
     * @return OrderDTO|null
     */
    public function update($id, $data) {
        $existing = $this->repository->findById($id);
        if (!$existing) {
            return null;
        }

        $updateData = [];
        if (isset($data['order_date']))       $updateData['order_date']       = $data['order_date'];
        if (isset($data['total_amount']))     $updateData['total_amount']     = floatval($data['total_amount']);
        if (isset($data['final_amount']))     $updateData['final_amount']     = floatval($data['final_amount']);
        if (isset($data['shipping_address'])) $updateData['shipping_address'] = trim($data['shipping_address']);
        if (isset($data['status']))           $updateData['status']           = trim($data['status']);
        if (isset($data['user_id']))          $updateData['user_id']          = intval($data['user_id']);
        if (isset($data['promotion_id']))     $updateData['promotion_id']     = intval($data['promotion_id']);
        if (isset($data['is_deleted']))       $updateData['is_deleted']       = intval($data['is_deleted']);

        $row = $this->repository->update($id, $updateData);
        return OrderMapper::toDTO($row);
    }

    /**
     * Xóa đơn hàng theo ID (soft delete)
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $existing = $this->repository->findById($id);
        if (!$existing) {
            return false;
        }

        return $this->repository->delete($id);
    }
}
?>