<?php
class OrderController extends BaseController {
    private $service;

    public function __construct() {
        $this->service = new OrderService();
    }

    /**
     * GET /api/orders
     * Lấy danh sách tất cả đơn hàng
     */
    public function index() {
        try {
            $orders = $this->service->getAll();
            $this->jsonResponse([
                'success' => true,
                'data' => $orders,
                'message' => 'Lấy danh sách đơn hàng thành công'
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi khi lấy danh sách đơn hàng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/orders/{id}
     * Lấy thông tin chi tiết một đơn hàng theo ID
     */
    public function show($id) {
        try {
            $order = $this->service->getById($id);
            if (!$order) {
                $this->jsonResponse([
                    'success' => false,
                    'data' => null,
                    'message' => "Không tìm thấy đơn hàng với ID $id"
                ], 404);
            }
            $this->jsonResponse([
                'success' => true,
                'data' => $order,
                'message' => 'Lấy thông tin đơn hàng thành công'
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi khi lấy thông tin đơn hàng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/orders
     * Tạo mới một đơn hàng
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

            $order = $this->service->create($data);
            $this->jsonResponse([
                'success' => true,
                'data' => $order,
                'message' => 'Tạo đơn hàng thành công'
            ], 201);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi khi tạo đơn hàng: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * PUT /api/orders/{id}
     * Cập nhật thông tin đơn hàng theo ID
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

            $order = $this->service->update($id, $data);
            if (!$order) {
                $this->jsonResponse([
                    'success' => false,
                    'data' => null,
                    'message' => "Không tìm thấy đơn hàng với ID $id"
                ], 404);
            }

            $this->jsonResponse([
                'success' => true,
                'data' => $order,
                'message' => "Cập nhật đơn hàng ID $id thành công"
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi khi cập nhật đơn hàng: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * DELETE /api/orders/{id}
     * Xóa đơn hàng theo ID
     */
    public function destroy($id) {
        try {
            $result = $this->service->delete($id);
            if (!$result) {
                $this->jsonResponse([
                    'success' => false,
                    'data' => null,
                    'message' => "Không tìm thấy đơn hàng với ID $id"
                ], 404);
            }

            $this->jsonResponse([
                'success' => true,
                'data' => null,
                'message' => "Xóa đơn hàng ID $id thành công"
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi khi xóa đơn hàng: ' . $e->getMessage()
            ], 500);
        }
    }
}
?>
