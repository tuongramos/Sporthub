<?php
class PaymentService {
    private $repository;

    public function __construct() {
        $this->repository = new PaymentRepository();
    }

    public function getAll() {
        $rows = $this->repository->findAll();
        $dtos = [];
        foreach ($rows as $row) {
            $dtos[] = PaymentMapper::toDTO($row);
        }
        return $dtos;
    }

    public function getById($id) {
        $row = $this->repository->findById($id);
        if (!$row) {
            return null;
        }
        return PaymentMapper::toDTO($row);
    }

    public function create($data) {
        if (empty($data['order_id'])) {
            throw new Exception('Order ID không được để trống');
        }
        if (!isset($data['amount'])) {
            throw new Exception('Số tiền thanh toán không được để trống');
        }

        $paymentData = [
            'amount'         => floatval($data['amount']),
            'payment_method' => isset($data['payment_method']) ? trim($data['payment_method']) : 'CASH',
            'payment_date'   => isset($data['payment_date']) ? $data['payment_date'] : date('Y-m-d H:i:s'),
            'status'         => isset($data['status']) ? trim($data['status']) : 'PENDING',
            'transaction_id' => isset($data['transaction_id']) ? trim($data['transaction_id']) : null,
            'order_id'       => intval($data['order_id'])
        ];

        $row = $this->repository->create($paymentData);
        return PaymentMapper::toDTO($row);
    }

    public function update($id, $data) {
        $existing = $this->repository->findById($id);
        if (!$existing) {
            return null;
        }

        $updateData = [];
        if (isset($data['amount']))         $updateData['amount']         = floatval($data['amount']);
        if (isset($data['payment_method'])) $updateData['payment_method'] = trim($data['payment_method']);
        if (isset($data['payment_date']))   $updateData['payment_date']   = $data['payment_date'];
        if (isset($data['status']))         $updateData['status']         = trim($data['status']);
        if (isset($data['transaction_id'])) $updateData['transaction_id'] = trim($data['transaction_id']);
        if (isset($data['order_id']))       $updateData['order_id']       = intval($data['order_id']);
        if (isset($data['is_deleted']))     $updateData['is_deleted']     = intval($data['is_deleted']);

        $row = $this->repository->update($id, $updateData);
        return PaymentMapper::toDTO($row);
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