<?php
class PromotionController extends BaseController {
    private $service;

    public function __construct() {
        $this->service = new PromotionService();
    }

    public function index() {
        try {
            $promotions = $this->service->getAll();
            return $this->jsonResponse([
                'success' => true,
                'data' => $promotions,
                'message' => 'Lấy danh sách khuyến mãi thành công'
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi khi lấy danh sách khuyến mãi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id) {
        try {
            $promotion = $this->service->getById($id);
            if (!$promotion) {
                $this->jsonResponse([
                    'success' => false,
                    'data' => null,
                    'message' => "Không tìm thấy khuyến mãi với ID $id"
                ], 404);
            }
            $this->jsonResponse([
                'success' => true,
                'data' => $promotion,
                'message' => 'Lấy thông tin khuyến mãi thành công'
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi khi lấy thông tin khuyến mãi: ' . $e->getMessage()
            ], 500);
        }
    }

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

            $promotion = $this->service->create($data);
            $this->jsonResponse([
                'success' => true,
                'data' => $promotion,
                'message' => 'Tạo khuyến mãi thành công'
            ], 201);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi khi tạo khuyến mãi: ' . $e->getMessage()
            ], 400);
        }
    }

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

            $promotion = $this->service->update($id, $data);
            if (!$promotion) {
                $this->jsonResponse([
                    'success' => false,
                    'data' => null,
                    'message' => "Không tìm thấy khuyến mãi với ID $id"
                ], 404);
            }

            $this->jsonResponse([
                'success' => true,
                'data' => $promotion,
                'message' => "Cập nhật khuyến mãi ID $id thành công"
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi khi cập nhật khuyến mãi: ' . $e->getMessage()
            ], 400);
        }
    }

    public function destroy($id) {
        try {
            $result = $this->service->delete($id);
            if (!$result) {
                $this->jsonResponse([
                    'success' => false,
                    'data' => null,
                    'message' => "Không tìm thấy khuyến mãi với ID $id"
                ], 404);
            }

            $this->jsonResponse([
                'success' => true,
                'data' => null,
                'message' => "Xóa khuyến mãi ID $id thành công"
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi khi xóa khuyến mãi: ' . $e->getMessage()
            ], 500);
        }
    }
}
?>
