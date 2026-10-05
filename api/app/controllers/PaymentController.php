<?php
class PaymentController extends BaseController {
    private $service;

    public function __construct() {
        $this->service = new PaymentService();
    }

    /**
     * GET /api/payments
     * Lấy danh sách tất cả thanh toán
     */
    public function index() {
        try {
            $payments = $this->service->getAll();
            $this->jsonResponse([
                'success' => true,
                'data' => $payments,
                'message' => 'Lấy danh sách thanh toán thành công'
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi khi lấy danh sách thanh toán: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/payments/{id}
     * Lấy thông tin thanh toán theo ID
     */
    public function show($id) {
        try {
            $payment = $this->service->getById($id);
            if (!$payment) {
                $this->jsonResponse([
                    'success' => false,
                    'data' => null,
                    'message' => "Không tìm thấy thanh toán với ID $id"
                ], 404);
            }
            $this->jsonResponse([
                'success' => true,
                'data' => $payment,
                'message' => 'Lấy thông tin thanh toán thành công'
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi khi lấy thông tin thanh toán: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/payments
     * Tạo mới thanh toán
     */
    public function store() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (empty($data)) {
                $this->jsonResponse([
                    'success' => false,
                    'data' => null,
                    'message' => 'Dữ liệu gửi lên không hợp lệ'
                ], 400);
            }

            $payment = $this->service->create($data);
            $this->jsonResponse([
                'success' => true,
                'data' => $payment,
                'message' => 'Tạo thanh toán thành công'
            ], 201);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi khi tạo thanh toán: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * PUT /api/payments/{id}
     * Cập nhật thông tin thanh toán theo ID
     */
    public function update($id) {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (empty($data)) {
                $this->jsonResponse([
                    'success' => false,
                    'data' => null,
                    'message' => 'Dữ liệu gửi lên không hợp lệ'
                ], 400);
            }

            $payment = $this->service->update($id, $data);
            if (!$payment) {
                $this->jsonResponse([
                    'success' => false,
                    'data' => null,
                    'message' => "Không tìm thấy thanh toán với ID $id"
                ], 404);
            }

            $this->jsonResponse([
                'success' => true,
                'data' => $payment,
                'message' => "Cập nhật thanh toán ID $id thành công"
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi khi cập nhật thanh toán: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * DELETE /api/payments/{id}
     * Xóa thanh toán theo ID
     */
    public function destroy($id) {
        try {
            $result = $this->service->delete($id);
            if (!$result) {
                $this->jsonResponse([
                    'success' => false,
                    'data' => null,
                    'message' => "Không tìm thấy thanh toán với ID $id"
                ], 404);
            }

            $this->jsonResponse([
                'success' => true,
                'data' => null,
                'message' => "Xóa thanh toán ID $id thành công"
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi khi xóa thanh toán: ' . $e->getMessage()
            ], 500);
        }
    }
}
?>
