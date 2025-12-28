<?php 
require_once '../SQL/Database.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}
$user_id = $_SESSION['user_id'];
$conn = Database::getInstance()->getConnection();
$stmt = $conn->prepare("
   SELECT o.order_id, o.order_date, o.order_status, 
       SUM(od.price_atpurchase) AS total_amount,
       GROUP_CONCAT(p.product_name SEPARATOR ', ') AS products
FROM orders o
JOIN order_details od ON o.order_id = od.order_id
JOIN product_variant v ON od.variant_id = v.variant_id
JOIN products p ON v.product_id = p.product_id
WHERE o.user_id = ?
GROUP BY o.order_id
ORDER BY o.order_date DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders | Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body { background-color: #f4f7f6; font-family: 'Poppins', sans-serif; }
        .order-card {
            border: none;
            border-radius: 15px;
            transition: transform 0.2s, box-shadow 0.2s;
            background: #fff;
        }
        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
        }
        .status-badge {
            padding: 6px 15px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-pending { background: #fff4e5; color: #ff9800; }
        .status-delivered { background: #e8f5e9; color: #2e7d32; }
        .status-shipped { background: #e3f2fd; color: #1976d2; }
        .order-header { border-bottom: 1px solid #f1f1f1; padding-bottom: 15px; }
        .product-list { color: #666; font-size: 0.95rem; line-height: 1.6; }
        .total-price { font-size: 1.2rem; font-weight: 700; color: #333; }
        .btn-view {
            border-radius: 8px;
            padding: 8px 20px;
            font-weight: 600;
            transition: 0.3s;
        }
       
body { 
    margin: 0; 
    padding: 0 !important; 

}

header {
    margin-top: 0px !important;
}



.animsition {
    padding-top: 0 !important;
}
    </style>
    
	<title>Home</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
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
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold">My Orders</h2>
            <p class="text-muted">Manage and track your recent purchases</p>
        </div>
        <div class="col-md-6 text-md-end align-self-center">
            <a href="product.php" class="btn btn-outline-dark rounded-pill px-4">Continue Shopping</a>
        </div>
    </div>

    <?php if (empty($orders)): ?>
        <div class="text-center py-5 bg-white rounded shadow-sm">
            <i class="fa-solid fa-box-open fa-3x text-muted mb-3"></i>
            <h4>No orders yet</h4>
            <p class="text-muted">When you buy items, they will appear here.</p>
            <a href="shop.php" class="btn btn-dark px-5 mt-2">Browse Products</a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($orders as $order): 
                // Determine CSS class based on status
                $statusClass = 'status-' . strtolower($order['order_status']);
            ?>
                <div class="col-12 mb-4">
                    <div class="card order-card shadow-sm p-4">
                        <div class="d-flex justify-content-between align-items-center order-header mb-3">
                            <div>
                                <span class="text-muted small text-uppercase">Order ID</span>
                                <h6 class="mb-0 fw-bold">#<?= $order['order_id'] ?></h6>
                            </div>
                            <div>
                                <span class="text-muted small text-uppercase d-block text-end">Date</span>
                                <h6 class="mb-0 fw-bold"><?= date('M d, Y', strtotime($order['order_date'])) ?></h6>
                            </div>
                            <div class="ms-4">
                                <span class="status-badge <?= $statusClass ?>">
                                    <?= htmlspecialchars($order['order_status'] ?? 'Pending') ?>
                                </span>
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <p class="mb-1 fw-semibold text-dark">Items:</p>
                                <p class="product-list mb-0">
                                    <i class="fa-solid fa-cart-shopping me-2 text-muted"></i>
                                    
                                    <?= htmlspecialchars($order['products']) ?>
                                </p>
                            </div>
                            <div class="col-md-2 text-md-center mt-3 mt-md-0">
                                <span class="text-muted small d-block">Total</span>
                                <span class="total-price">$<?= number_format($order['total_amount'], 2) ?></span>
                            </div>
                            <div class="col-md-2 text-md-end mt-3 mt-md-0">
                                <a href="order_details.php?id=<?= $order['order_id'] ?>" class="btn btn-dark btn-view w-100">
                                    Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include 'footer.php'; ?>
</body>
</html>









