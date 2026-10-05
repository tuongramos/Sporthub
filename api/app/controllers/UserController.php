<?php

class UserController extends BaseController {

    private $service;

    public function __construct() {
        $this->service = new UserService();
    }

    /**
     * GET /api/users
     * Lấy danh sách tất cả người dùng
     */
    public function index() {
        try {
            $users = $this->service->getAll();
            return $this->jsonResponse([
                'success' => true,
                'data' => $users,
                'message' => 'Lấy danh sách người dùng thành công'
            ]);
        } catch (Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi khi lấy danh sách người dùng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/users/{id}
     * Lấy thông tin người dùng theo ID
     */
    public function show($id) {
        try {
            $user = $this->service->getById($id);
            if (!$user) {
                $this->jsonResponse([
                    'success' => false,
                    'data' => null,
                    'message' => "Không tìm thấy người dùng với ID $id"
                ], 404);
            }
            $this->jsonResponse([
                'success' => true,
                'data' => $user,
                'message' => 'Lấy thông tin người dùng thành công'
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi khi lấy thông tin người dùng: ' . $e->getMessage()
            ], 500);
        }
    }

    public function login() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (empty($data)) {
                $this->jsonResponse([
                    'success' => false,
                    'data' => null,
                    'message' => 'Dữ liệu không hợp lệ'
                ], 400);
                return;
            }

            $username = $data['username'] ?? '';
            $password = $data['password'] ?? '';

            $user = $this->service->login($username, $password);

            $this->jsonResponse([
                'success' => true,
                'data' => $user,
                'message' => 'Đăng nhập thành công'
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => $e->getMessage()
            ], 401);
        }
    }

    /**
     * POST /api/users
     * Tạo mới người dùng
     */
    public function store() {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (empty($data)) {
                $this->jsonResponse([
                    'success' => false,
                    'data' => null,
                    'message' => 'Dữ liệu gửi lên không hợp lệ'
                ], 400);
            }

            $result = $this->service->create($data);
            if ($result) {
                $this->jsonResponse([
                    'success' => true,
                    'data' => null,
                    'message' => 'Tạo người dùng thành công'
                ], 201);
            } else {
                $this->jsonResponse([
                    'success' => false,
                    'data' => null,
                    'message' => 'Tạo người dùng thất bại'
                ], 400);
            }
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi khi tạo người dùng: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * PUT /api/users/{id}
     * Cập nhật thông tin người dùng theo ID
     */
    public function update($id) {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (empty($data)) {
                $this->jsonResponse([
                    'success' => false,
                    'data' => null,
                    'message' => 'Dữ liệu gửi lên không hợp lệ'
                ], 400);
            }

            $result = $this->service->update($id, $data);
            if ($result === null) {
                $this->jsonResponse([
                    'success' => false,
                    'data' => null,
                    'message' => "Không tìm thấy người dùng với ID $id"
                ], 404);
            }

            $this->jsonResponse([
                'success' => true,
                'data' => null,
                'message' => "Cập nhật người dùng ID $id thành công"
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi khi cập nhật người dùng: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * DELETE /api/users/{id}
     * Xóa người dùng theo ID
     */
    public function destroy($id) {
        try {
            $result = $this->service->delete($id);
            if (!$result) {
                $this->jsonResponse([
                    'success' => false,
                    'data' => null,
                    'message' => "Không tìm thấy người dùng với ID $id"
                ], 404);
            }

            $this->jsonResponse([
                'success' => true,
                'data' => null,
                'message' => "Xóa người dùng ID $id thành công"
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'data' => null,
                'message' => 'Lỗi khi xóa người dùng: ' . $e->getMessage()
            ], 500);
        }
    }
}
?>