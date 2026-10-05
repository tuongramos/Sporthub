<?php
class ProductController extends BaseController {
    private $service;

    public function __construct() {
        $this->service = new ProductService();
    }

    /**
     * GET /api/products
     * Hỗ trợ query params: ?category_id=X và ?search=keyword
     */
    public function index() {
        try {
            if (isset($_GET['category_id']) && $_GET['category_id'] !== '') {
                $categoryId = $_GET['category_id'];
                $products = $this->service->getByCategory($categoryId);
                return $this->jsonResponse([
                    'success' => true,
                    'data' => $products,
                    'message' => 'Lấy danh sách sản phẩm theo danh mục thành công'
                ]);
            }

            if (isset($_GET['search']) && $_GET['search'] !== '') {
                $keyword = $_GET['search'];
                $products = $this->service->search($keyword);
                return $this->jsonResponse([
                    'success' => true,
                    'data' => $products,
                    'message' => 'Tìm kiếm sản phẩm thành công'
                ]);
            }

            $products = $this->service->getAll();
            return $this->jsonResponse([
                'success' => true,
                'data' => $products,
                'message' => 'Lấy danh sách sản phẩm thành công'
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/products/{id}
     * Lấy thông tin chi tiết 1 sản phẩm
     */
    public function show($id) {
        try {
            $product = $this->service->getById($id);
            if (!$product) {
                $this->jsonResponse([
                    'success' => false,
                    'data' => null,
                    'message' => 'Không tìm thấy sản phẩm'
                ], 404);
            }
            $this->jsonResponse([
                'success' => true,
                'data' => $product,
                'message' => 'Lấy thông tin sản phẩm thành công'
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/products
     * Tạo sản phẩm mới từ JSON body
     */
    public function store() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$data) {
                $this->jsonResponse([
                    'success' => false,
                    'data' => null,
                    'message' => 'Dữ liệu không hợp lệ'
                ], 400);
            }

            $result = $this->service->create($data);

            if ($result['success']) {
                $this->jsonResponse([
                    'success' => true,
                    'data' => ['id' => $result['id']],
                    'message' => $result['message']
                ], 201);
            } else {
                $this->jsonResponse([
                    'success' => false,
                    'data' => null,
                    'message' => $result['message']
                ], 400);
            }
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * PUT /api/products/{id}
     * Cập nhật sản phẩm theo ID từ JSON body
     */
    public function update($id) {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$data) {
                $this->jsonResponse([
                    'success' => false,
                    'data' => null,
                    'message' => 'Dữ liệu không hợp lệ'
                ], 400);
            }

            $result = $this->service->update($id, $data);

            if ($result['success']) {
                $this->jsonResponse([
                    'success' => true,
                    'data' => null,
                    'message' => $result['message']
                ]);
            } else {
                $this->jsonResponse([
                    'success' => false,
                    'data' => null,
                    'message' => $result['message']
                ], 400);
            }
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * DELETE /api/products/{id}
     * Xóa sản phẩm theo ID
     */
    public function destroy($id) {
        try {
            $result = $this->service->delete($id);

            if ($result['success']) {
                $this->jsonResponse([
                    'success' => true,
                    'data' => null,
                    'message' => $result['message']
                ]);
            } else {
                $this->jsonResponse([
                    'success' => false,
                    'data' => null,
                    'message' => $result['message']
                ], 404);
            }
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
}
?>
