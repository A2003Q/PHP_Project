<?php
// models/Admin.php
require_once "../CRUD/CRUD.php";

class Admin extends CRUD {

    public function getAdminById($id) {
        $sql = "SELECT * FROM users 
                WHERE user_id = $id AND user_role = 'admin'";
        return $this->read($sql);
    }

    public function updateAdmin($id, $name, $email, $phone, $password = null) {

        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $sql = "UPDATE users SET
                        user_name = '$name',
                        user_email = '$email',
                        user_phone = '$phone',
                        user_password = '$hashedPassword'
                    WHERE user_id = $id AND user_role = 'admin'";
        } else {
            $sql = "UPDATE users SET
                        user_name = '$name',
                        user_email = '$email',
                        user_phone = '$phone'
                    WHERE user_id = $id AND user_role = 'admin'";
        }

        return $this->update($sql);
    }
}




  