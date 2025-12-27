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
    ci.cart_items_id AS cart_items_id,
    ci.cart_items_quantity,
    v.variant_id,
    v.size,
    v.color,
    p.product_id,
    p.product_name
FROM orders o
JOIN order_details od ON od.order_id = o.order_id
JOIN cart_items ci ON ci.cart_id = o.cart_id
JOIN product_variant v ON v.variant_id = ci.variant_id
JOIN products p ON p.product_id = v.product_id
WHERE o.order_id = $order_id
";


    return $this->read($sql);
}

}
?>
