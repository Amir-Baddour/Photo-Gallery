<?php
class UserSkeleton {
    private $id;
    private $fullname;
    private $email;
    private $password;
    private $created_at;

    public function __construct($id = null, $fullname = null, $email = null, $password = null, $created_at = null) {
        $this->id = $id;
        $this->fullname = $fullname;
        $this->email = $email;
        $this->password = $password;
        $this->created_at = $created_at;
    }

    // Getters and setters
    public function getId() {
        return $this->id;
    }
    public function setId($id) {
        $this->id = $id;
    }

    public function getFullname() {
        return $this->fullname;
    }
    public function setFullname($fullname) {
        $this->fullname = $fullname;
    }

    public function getEmail() {
        return $this->email;
    }
    public function setEmail($email) {
        $this->email = $email;
    }

    public function getPassword() {
        return $this->password;
    }
    public function setPassword($password) {
        $this->password = $password;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }
    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }
}
?>
