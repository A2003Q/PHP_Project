<?php
require_once "../php/orders.php";

$orderModel = new Orders();
$order_id = $_GET['order_id'] ?? 0;

$data = [];
$result = $orderModel->getOrderDetails($order_id);

while($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
?>
