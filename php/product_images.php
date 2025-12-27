<?php
require_once "../CRUD/CRUD.php";

class ProductImages extends CRUD {

    public function getByProduct($productId) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM product_images WHERE product_id = ?"
        );
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function add($productId, $imageUrl) {
        $stmt = $this->conn->prepare(
            "INSERT INTO product_images (product_id, image_url)
             VALUES (?, ?)"
        );
        $stmt->bind_param("is", $productId, $imageUrl);
        return $stmt->execute();
    }

    public function updateImage($imageId, $imageUrl) {
        $stmt = $this->conn->prepare(
            "UPDATE product_images 
             SET image_url = ?
             WHERE product_images_id = ?"
        );
        $stmt->bind_param("si", $imageUrl, $imageId);
        return $stmt->execute();
    }

    public function deleteImage($imageId) {
        $stmt = $this->conn->prepare(
            "DELETE FROM product_images WHERE product_images_id = ?"
        );
        $stmt->bind_param("i", $imageId);
        return $stmt->execute();
    }
}
?>