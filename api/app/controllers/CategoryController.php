<?php
class CategoryController extends BaseController {
    private $service;

    public function __construct() {
        $this->service = new CategoryService();
    }

    /**
     * GET /api/categories
     * Lấy danh sách tất cả danh mục
     */
    public function index() {
        try {
            $categories = $this->service->getAll();
            $this->jsonResponse([
                'success' => true,
                'data' => $categories,
                'message' => 'Lấy danh sách danh mục thành công'
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi khi lấy danh sách danh mục: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/categories/{id}
     * Lấy thông tin chi tiết một danh mục theo ID
     */
    public function show($id) {
        try {
            $category = $this->service->getById($id);
            if (!$category) {
                $this->jsonResponse([
                    'success' => false,
                    'data' => null,
                    'message' => "Không tìm thấy danh mục với ID $id"
                ], 404);
            }
            $this->jsonResponse([
                'success' => true,
                'data' => $category,
                'message' => 'Lấy thông tin danh mục thành công'
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi khi lấy thông tin danh mục: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/categories
     * Tạo mới một danh mục
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

            $category = $this->service->create($data);
            $this->jsonResponse([
                'success' => true,
                'data' => $category,
                'message' => 'Tạo danh mục thành công'
            ], 201);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi khi tạo danh mục: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * PUT /api/categories/{id}
     * Cập nhật thông tin danh mục theo ID
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

            $category = $this->service->update($id, $data);
            if (!$category) {
                $this->jsonResponse([
                    'success' => false,
                    'data' => null,
                    'message' => "Không tìm thấy danh mục với ID $id"
                ], 404);
            }

            $this->jsonResponse([
                'success' => true,
                'data' => $category,
                'message' => "Cập nhật danh mục ID $id thành công"
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi khi cập nhật danh mục: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * DELETE /api/categories/{id}
     * Xóa danh mục theo ID
     */
    public function destroy($id) {
        try {
            $result = $this->service->delete($id);
            if (!$result) {
                $this->jsonResponse([
                    'success' => false,
                    'data' => null,
                    'message' => "Không tìm thấy danh mục với ID $id"
                ], 404);
            }

            $this->jsonResponse([
                'success' => true,
                'data' => null,
                'message' => "Xóa danh mục ID $id thành công"
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi khi xóa danh mục: ' . $e->getMessage()
            ], 500);
        }
    }
}
?>
