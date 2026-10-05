<?php
class User {
    private $id;
    private $username;
    private $email;
    private $password;
    private $full_name;
    private $phone;
    private $address;
    private $role;

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getUsername() { return $this->username; }
    public function setUsername($username) { $this->username = $username; }

    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; }

    public function getPassword() { return $this->password; }
    public function setPassword($password) { $this->password = $password; }

    public function getFullName() { return $this->full_name; }
    public function setFullName($full_name) { $this->full_name = $full_name; }

    public function getPhone() { return $this->phone; }
    public function setPhone($phone) { $this->phone = $phone; }

    public function getAddress() { return $this->address; }
    public function setAddress($address) { $this->address = $address; }

    public function getRole() { return $this->role; }
    public function setRole($role) { $this->role = $role; }
}
?>
