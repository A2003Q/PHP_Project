<?php
// models/Orders.php
require_once "../CRUD/CRUD.php";

class Orders extends CRUD {

    public function getAllOrders() {
        $sql = "SELECT o.*, u.user_name FROM orders o
                JOIN users u ON o.user_id = u.user_id";
        return $this->read($sql);
    }

    public function getOrderById($id) {
        $sql = "SELECT * FROM orders WHERE order_id=$id";
        return $this->read($sql);
    }

    public function addOrder($user_id, $product_id, $quantity) {
        $sql = "INSERT INTO orders (user_id, product_id, quantity) VALUES ($user_id, $product_id, $quantity)";
        return $this->create($sql);
    }

    public function updateOrder($id, $user_id, $product_id, $quantity) {
        $sql = "UPDATE orders SET user_id=$user_id, product_id=$product_id, quantity=$quantity WHERE order_id=$id";
        return $this->update($sql);
    }

    public function deleteOrder($id) {
        $sql = "DELETE FROM orders WHERE order_id=$id";
        return $this->delete($sql);
    }
}
?>
