<?php
// models/Categories.php
require_once "../CRUD/CRUD.php";

class Categories extends CRUD {

    public function getAllCategories() {
        $sql = "SELECT * FROM categories";
        return $this->read($sql);
    }

    public function getCategoryById($id) {
        $sql = "SELECT * FROM categories WHERE category_id=$id";
        return $this->read($sql);
    }

    public function addCategory($name) {
        $sql = "INSERT INTO categories (category_name) VALUES ('$name')";
        return $this->create($sql);
    }

    public function updateCategory($id, $name) {
        $sql = "UPDATE categories SET category_name='$name' WHERE category_id=$id";
        return $this->update($sql);
    }

    public function deleteCategory($id) {
        $sql = "DELETE FROM categories WHERE category_id=$id";
        return $this->delete($sql);
    }
}
?>
