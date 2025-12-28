<?php
require_once '../SQL/Database.php';
session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$order_id = (int)($_GET['id'] ?? 0);

if ($order_id <= 0) {
    die("Invalid order.");
}

$conn = Database::getInstance()->getConnection();

/* Fetch order info */
$orderStmt = $conn->prepare("
    SELECT order_id, order_date, order_status
    FROM orders
    WHERE order_id = ? AND user_id = ?
");
$orderStmt->bind_param("ii", $order_id, $user_id);
$orderStmt->execute();
$order = $orderStmt->get_result()->fetch_assoc();

if (!$order) {
    die("Order not found.");
}

/* Fetch order products */
$itemsStmt = $conn->prepare("
  SELECT
    od.order_details_id,
    p.product_id,
    p.product_name,
    v.size,
    v.color,
    od.price_atpurchase,
    v.quantity
FROM order_details od
JOIN product_variant v ON od.variant_id = v.variant_id
JOIN products p ON v.product_id = p.product_id
WHERE od.order_id = ?
");
$itemsStmt->bind_param("i", $order_id);
$itemsStmt->execute();
$items = $itemsStmt->get_result()->fetch_all(MYSQLI_ASSOC);


$total = 0;

foreach ($items as $item) {
    $total += $item['price_atpurchase'] * $item['quantity'];
}?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order #<?= $order['order_id'] ?></title>
    <link rel="stylesheet" href="order-details.css">
    <style>
    body { background-color: #f8f9fa; font-family: 'Poppins', sans-serif; }
    
    .detail-container { max-width: 900px; margin: 50px auto; }
    
    /* Card Styling */
    .detail-card {
        background: #fff;
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        overflow: hidden;
    }

    /* Header Section */
    .detail-header {
        background: #333;
        color: #fff;
        padding: 40px;
    }
    
    .status-pill {
        display: inline-block;
        padding: 5px 20px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
        background: rgba(255,255,255,0.2);
        color: #fff;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Table/List Styling */
    .item-row {
        padding: 20px 40px;
        border-bottom: 1px solid #eee;
        transition: background 0.3s;
    }
    .item-row:hover { background: #fafafa; }
    .item-row:last-child { border-bottom: none; }
    
    .product-img-placeholder {
        width: 60px;
        height: 60px;
        background: #f1f1f1;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ccc;
    }

    .price-text { font-weight: 600; color: #333; }
    .qty-badge {
        background: #eee;
        padding: 2px 10px;
        border-radius: 5px;
        font-size: 0.8rem;
        font-weight: bold;
    }

    /* Summary Section */
    .summary-box {
        background: #fdfdfd;
        padding: 30px 40px;
        border-top: 2px solid #f1f1f1;
    }
    
    .total-amount {
        font-size: 1.8rem;
        font-weight: 800;
        color: #000;
    }
</style>
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="images/icons/favicon.png"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/iconic/css/material-design-iconic-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/linearicons-v1.0.0/icon-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="vendor/daterangepicker/daterangepicker.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/slick/slick.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/MagnificPopup/magnific-popup.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/perfect-scrollbar/perfect-scrollbar.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
<!--===============================================================================================-->

</head>
<body>
    <?php include 'header-main.php'; ?>

<div class="order-container">

   <div class="container detail-container">
    <a href="my_orders.php" class="btn btn-link text-dark text-decoration-none mb-4 p-0">
        <i class="fa fa-arrow-left me-2"></i> Back to My Orders
    </a>

    <div class="card detail-card">
        <div class="detail-header d-flex justify-content-between align-items-center">
            <div>
                <h6 class="text-white-50 mb-1">Order Details</h6>
                <h2 class="mb-0 fw-bold">#<?= $order['order_id'] ?></h2>
            </div>
            <div class="text-end">
                <span class="status-pill mb-2 d-inline-block">
                    <?= htmlspecialchars($order['order_status']) ?>
                </span>
                <p class="mb-0 small text-white-50">
                    Placed on <?= date('F d, Y', strtotime($order['order_date'])) ?>
                </p>
            </div>
        </div>

        <div class="py-2">
            <?php foreach ($items as $item): ?>
                <div class="item-row d-flex align-items-center">
                    <div class="product-img-placeholder me-4">
                        <i class="fa fa-shopping-bag fa-lg"></i>

                    </div>

                    <div class="flex-grow-1">
                        <h6 class="mb-1 fw-bold"><?= htmlspecialchars($item['product_name']) ?></h6>
                        <small class="text-muted text-uppercase">
                            Size: <?= htmlspecialchars($item['size']) ?> | Color: <?= htmlspecialchars($item['color']) ?>
                        </small>
                    </div>

                    <div class="text-end" style="min-width: 150px;">
                        <span class="qty-badge me-2">x<?= $item['quantity'] ?></span>
                        <span class="price-text">$<?= number_format($item['price_atpurchase'] * $item['quantity'], 2) ?></span>
                        <div class="small text-muted">$<?= number_format($item['price_atpurchase'], 2) ?> each</div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="summary-box">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-muted small mb-0">Payment Method: Cash on Delivery</p>
                    <p class="text-muted small mb-0">Shipping: Free Standard Shipping</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="text-muted text-uppercase small fw-bold d-block">Total Amount Paid</span>
                    <span class="total-amount">$<?= number_format($total, 2) ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-4 text-center">
       
        <a href="contact.php" class="btn btn-outline-secondary btn-sm">
            <i class="fa fa-question-circle me-1"></i> Need help?
        </a>
    </div>
</div>

</body>
</html>
