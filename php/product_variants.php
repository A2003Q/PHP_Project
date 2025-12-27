<?php
require_once "../CRUD/CRUD.php";

class ProductVariants extends CRUD {

    public function getByProduct($productId) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM product_variant WHERE product_id = ?"
        );
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function add($productId, $size, $color, $qty) {
        $stmt = $this->conn->prepare(
            "INSERT INTO product_variant (product_id, size, color, quantity)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("issi", $productId, $size, $color, $qty);
        return $stmt->execute();
    }

    public function updateVariant($variantId, $size, $color, $qty) {
        $stmt = $this->conn->prepare(
            "UPDATE product_variant 
             SET size = ?, color = ?, quantity = ?
             WHERE variant_id = ?"
        );
        $stmt->bind_param("ssii", $size, $color, $qty, $variantId);
        return $stmt->execute();
    }

    public function deleteVariant($variantId) {
        $stmt = $this->conn->prepare(
            "DELETE FROM product_variant WHERE variant_id = ?"
        );
        $stmt->bind_param("i", $variantId);
        return $stmt->execute();
    }
}
?>