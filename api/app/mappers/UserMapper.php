<?php
class UserMapper {
    /**
     * Chuyển đổi dữ liệu từ DB row (mảng kết hợp) sang UserDTO
     * @param array $row - Dòng dữ liệu từ PDO fetch
     * @return UserDTO|null
     */
    public static function toDTO($row) {
        if (!$row) return null;

        $dto = new UserDTO();
        $dto->id           = $row['id'] ?? null;
        $dto->username     = $row['username'] ?? null;
        $dto->email        = $row['email'] ?? null;
        $dto->first_name   = $row['first_name'] ?? null;
        $dto->last_name    = $row['last_name'] ?? null;
        $dto->phone_number = $row['phone_number'] ?? null;
        $dto->address      = $row['address'] ?? null;
        $dto->sex          = $row['sex'] ?? null;
        $dto->role         = $row['role'] ?? null;
        $dto->status       = $row['status'] ?? null;
        $dto->created_at   = $row['created_at'] ?? null;

        return $dto;
    }
}
?>
