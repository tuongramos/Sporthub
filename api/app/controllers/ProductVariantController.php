<?php
class ProductVariantController extends BaseController {
    private $service;

    public function __construct() {
        $this->service = new ProductVariantService();
    }

    public function index() {
        try {
            $variants = $this->service->getAll();
            $this->jsonResponse([
                'success' => true,
                'data' => $variants,
                'message' => 'Lấy danh sách biến thể thành công'
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getByProductId($productId) {
        $variants = $this->service->getByProductId($productId);
        $this->jsonResponse($variants);
    }

    public function show($id) {
        $variant = $this->service->getById($id);
        $this->jsonResponse($variant);
    }

    public function store() {
        $data = json_decode(file_get_contents("php://input"), true);
        $result = $this->service->create($data);

        if ($result['success']) {
            $this->jsonResponse($result, 201);
        } else {
            $this->jsonResponse($result, 400);
        }
    }

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"), true);
        $result = $this->service->update($id, $data);

        if ($result['success']) {
            $this->jsonResponse($result);
        } else {
            $this->jsonResponse($result, 400);
        }
    }

    public function destroy($id) {
        $result = $this->service->delete($id);

        if ($result['success']) {
            $this->jsonResponse($result);
        } else {
            $this->jsonResponse($result, 400);
        }
    }
}
?>
