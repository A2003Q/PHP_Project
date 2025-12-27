<?php
session_start();
require_once "../php/Admin.php";

require_once "../SQL/Database.php";
$conn = Database::getInstance()->getConnection();

// COUNTS
$userCount    = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$orderCount   = $conn->query("SELECT COUNT(*) AS total FROM orders")->fetch_assoc()['total'];
$productCount = $conn->query("SELECT COUNT(*) AS total FROM products")->fetch_assoc()['total'];

// TOTAL REVENUE
$totalRevenue = $conn->query("SELECT SUM(order_totalprice) AS revenue FROM orders")->fetch_assoc()['revenue'];






if (!isset($_SESSION['user_id'])) {
    header("Location: ../main_page/auth/login.php");
    exit;
}

$adminId = $_SESSION['user_id'];
$adminModel = new Admin();
$result = $adminModel->getAdminById($adminId);
$admin = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
body {
    background: #f4f4f4;
    font-family: Arial, sans-serif;
}

#content h2 {
    color: #4f3131; /* one of your gradient colors */
    font-weight: bold;
}

.dashboard-row {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 30px;
}

.dashboard-card {
    background: linear-gradient(135deg, #686868ff, #4f3131);
    color: #fff;
    padding: 30px;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 10px 25px rgba(0,0,0,0.25);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    width: 45%; /* 2 per row */
    min-width: 250px;
}
.dashboard-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.35);
}
.dashboard-card i {
    font-size: 40px;
    margin-bottom: 15px;
}
.dashboard-card h4 {
    font-size: 22px;
    margin-bottom: 10px;
}
.dashboard-card p {
    font-size: 28px;
    font-weight: bold;
    margin: 0;
}
</style>

</head>
<body>
    <div id="wrapper">
    <?php include "../php/sidebar.php"; ?>

   
</div>

<div id="content" class="container-fluid">

    <h2 class="text-center mt-4 mb-5">
        Welcome, <?= htmlspecialchars($admin['user_name']) ?>!
    </h2>

    <div class="dashboard-row">

        <!-- Users -->
        <div class="dashboard-card">
            <i class="fa fa-users"></i>
            <h4>Users</h4>
            <p><?= $userCount ?></p>
        </div>

        <!-- Orders -->
        <div class="dashboard-card">
            <i class="fa fa-box"></i>
            <h4>Orders</h4>
            <p><?= $orderCount ?></p>
        </div>

        <!-- Products -->
        <div class="dashboard-card">
            <i class="fa fa-shirt"></i>
            <h4>Products</h4>
            <p><?= $productCount ?></p>
        </div>

        <!-- Revenue -->
        <div class="dashboard-card">
            <i class="fa fa-dollar-sign"></i>
            <h4>Total Revenue</h4>
            <p>$<?= number_format($totalRevenue, 2) ?></p>
        </div>

    </div>
</div>


</div>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

