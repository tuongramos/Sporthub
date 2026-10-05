<?php
class OrderDetailController extends BaseController {
    private $service;

    public function __construct() {
        $this->service = new OrderDetailService();
    }

    /**
     * GET /api/order-details
     * Lấy danh sách tất cả chi tiết đơn hàng
     */
    public function index() {
        try {
            $details = $this->service->getAll();
            $this->jsonResponse([
                'success' => true,
                'data' => $details,
                'message' => 'Lấy danh sách chi tiết đơn hàng thành công'
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi khi lấy danh sách chi tiết đơn hàng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/order-details/{id}
     * Lấy thông tin chi tiết đơn hàng theo ID
     */
    public function show($id) {
        try {
            $detail = $this->service->getById($id);
            if (!$detail) {
                $this->jsonResponse([
                    'success' => false,
                    'data' => null,
                    'message' => "Không tìm thấy chi tiết đơn hàng với ID $id"
                ], 404);
            }
            $this->jsonResponse([
                'success' => true,
                'data' => $detail,
                'message' => 'Lấy thông tin chi tiết đơn hàng thành công'
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi khi lấy thông tin chi tiết đơn hàng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/order-details
     * Tạo mới chi tiết đơn hàng
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

            $detail = $this->service->create($data);
            $this->jsonResponse([
                'success' => true,
                'data' => $detail,
                'message' => 'Tạo chi tiết đơn hàng thành công'
            ], 201);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi khi tạo chi tiết đơn hàng: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * PUT /api/order-details/{id}
     * Cập nhật chi tiết đơn hàng theo ID
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

            $detail = $this->service->update($id, $data);
            if (!$detail) {
                $this->jsonResponse([
                    'success' => false,
                    'data' => null,
                    'message' => "Không tìm thấy chi tiết đơn hàng với ID $id"
                ], 404);
            }

            $this->jsonResponse([
                'success' => true,
                'data' => $detail,
                'message' => "Cập nhật chi tiết đơn hàng ID $id thành công"
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi khi cập nhật chi tiết đơn hàng: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * DELETE /api/order-details/{id}
     * Xóa chi tiết đơn hàng theo ID
     */
    public function destroy($id) {
        try {
            $result = $this->service->delete($id);
            if (!$result) {
                $this->jsonResponse([
                    'success' => false,
                    'data' => null,
                    'message' => "Không tìm thấy chi tiết đơn hàng với ID $id"
                ], 404);
            }

            $this->jsonResponse([
                'success' => true,
                'data' => null,
                'message' => "Xóa chi tiết đơn hàng ID $id thành công"
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi khi xóa chi tiết đơn hàng: ' . $e->getMessage()
            ], 500);
        }
    }
}
?>