<?php
// models/Categories.php
require_once "../CRUD/CRUD.php";

class Categories extends CRUD {

    public function getAllCategories() {
        $sql = "SELECT * FROM categories";
        return $this->read($sql);
    }

    public function getCategoryById($id) {
        $sql = "SELECT * FROM categories WHERE categories_id=$id";
        return $this->read($sql);
    }

    public function addCategory($name, $description, $picture ) {
        $sql = "INSERT INTO 
        categories (categories_name,categories_description ,categories_picture) VALUES ('$name', '$description', '$picture')";

        return $this->create($sql);
    }

    public function updateCategory($id, $name, $description, $picture) {
        $sql = "UPDATE categories SET categories_name='$name', categories_description='$description', categories_picture='$picture'
         WHERE categories_id=$id";
        return $this->update($sql);
    }

    public function deleteCategory($id) {
        $sql = "DELETE FROM categories WHERE categories_id=$id";
        return $this->delete($sql);
    }
}
?>
