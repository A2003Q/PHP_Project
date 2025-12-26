<?php
require_once "../CRUD/CRUD.php";

class Products extends CRUD {

    public function getAllProducts() {
        return $this->read("SELECT * FROM products ORDER BY product_id DESC");
    }

    public function addProduct($name, $price, $description, $quantity, $discount) {
        $stmt = $this->conn->prepare(
            "INSERT INTO products 
            (product_name, product_price, product_description, product_quantity, product_discount)
            VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sdsii", $name, $price, $description, $quantity, $discount);
        return $stmt->execute();
    }

    public function updateProduct($id, $name, $price, $description, $quantity, $discount) {
        $stmt = $this->conn->prepare(
            "UPDATE products SET 
            product_name=?, product_price=?, product_description=?, 
            product_quantity=?, product_discount=?
            WHERE product_id=?"
        );
        $stmt->bind_param("sdsiii", $name, $price, $description, $quantity, $discount, $id);
        return $stmt->execute();
    }

    public function deleteProduct($id) {
        $stmt = $this->conn->prepare("DELETE FROM products WHERE product_id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

