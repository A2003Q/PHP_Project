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
public function getOrderDetails($order_id) {
    $sql = "
    SELECT 
        od.order_details_id,
        od.price_atpurchase,
        od.variant_id,
        v.size,
        v.color,
        v.quantity AS variant_quantity,
        p.product_id,
        p.product_name,
        p.product_price
    FROM order_details od
    JOIN product_variant v ON v.variant_id = od.variant_id
    JOIN products p ON p.product_id = v.product_id
    WHERE od.order_id = $order_id
    ";
    return $this->read($sql);
}



}
?>
