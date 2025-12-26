<?php
// models/Users.php
require_once "../CRUD/CRUD.php";

class Users extends CRUD {

    public function getAllUsers() {
        $sql = "SELECT * FROM users WHERE user_role != 'admin'";
        return $this->read($sql);
    }

    public function getUserById($id) {
        $sql = "SELECT * FROM users WHERE user_id = $id AND user_role != 'admin'";
        return $this->read($sql);
    }

    public function addUser($name, $email, $phone, $password) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (user_name, user_email, user_phone, user_password, user_role)
                VALUES ('$name','$email','$phone','$hashed','user')";
        return $this->create($sql);
    }

    public function updateUser($id, $name, $email, $phone, $password = null) {
        if ($password) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET user_name='$name', user_email='$email', user_phone='$phone', user_password='$hashed' 
                    WHERE user_id=$id AND user_role != 'admin'";
        } else {
            $sql = "UPDATE users SET user_name='$name', user_email='$email', user_phone='$phone' 
                    WHERE user_id=$id AND user_role != 'admin'";
        }
        return $this->update($sql);
    }

    public function deleteUser($id) {
        $sql = "DELETE FROM users WHERE user_id=$id AND user_role != 'admin'";
        return $this->delete($sql);
    }
}
?>


