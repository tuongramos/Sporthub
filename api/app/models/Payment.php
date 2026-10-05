<?php
class Payment {
    private $id;
    private $amount;
    private $payment_method;
    private $payment_date;
    private $status;
    private $transaction_id;
    private $order_id;
    private $is_deleted;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id             = $data['id'] ?? null;
            $this->amount         = $data['amount'] ?? null;
            $this->payment_method = $data['payment_method'] ?? null;
            $this->payment_date   = $data['payment_date'] ?? null;
            $this->status         = $data['status'] ?? null;
            $this->transaction_id = $data['transaction_id'] ?? null;
            $this->order_id       = $data['order_id'] ?? null;
            $this->is_deleted     = $data['is_deleted'] ?? 0;
        }
    }

    // Getters
    public function getId()            { return $this->id; }
    public function getAmount()        { return $this->amount; }
    public function getPaymentMethod() { return $this->payment_method; }
    public function getPaymentDate()   { return $this->payment_date; }
    public function getStatus()        { return $this->status; }
    public function getTransactionId() { return $this->transaction_id; }
    public function getOrderId()       { return $this->order_id; }
    public function getIsDeleted()     { return $this->is_deleted; }

    // Setters
    public function setId($id)                         { $this->id = $id; }
    public function setAmount($amount)                 { $this->amount = $amount; }
    public function setPaymentMethod($payment_method)  { $this->payment_method = $payment_method; }
    public function setPaymentDate($payment_date)      { $this->payment_date = $payment_date; }
    public function setStatus($status)                 { $this->status = $status; }
    public function setTransactionId($transaction_id)  { $this->transaction_id = $transaction_id; }
    public function setOrderId($order_id)              { $this->order_id = $order_id; }
    public function setIsDeleted($is_deleted)          { $this->is_deleted = $is_deleted; }
}
?>