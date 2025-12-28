<?php
session_start(); //A session allows you to store data on the server for a user, so you can keep track of that user across different pages.
//Unlike cookies (stored on the browser), session data is stored on the server.
require_once "../php/Admin.php";

require_once "../SQL/Database.php";
$conn = Database::getInstance()->getConnection();

// COUNTS
$userCount    = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$orderCount   = $conn->query("SELECT COUNT(*) AS total FROM orders")->fetch_assoc()['total'];
$productCount = $conn->query("SELECT COUNT(*) AS total FROM products")->fetch_assoc()['total'];

// TOTAL REVENUE
$totalRevenue = $conn->query("SELECT SUM(order_totalprice) AS revenue FROM orders")->fetch_assoc()['revenue'];

// ORDER STATUS COUNTS
$statusQuery = "
    SELECT order_status, COUNT(*) AS total
    FROM orders
    GROUP BY order_status
";
$statusResult = $conn->query($statusQuery);

$orderStatusData = [];
while ($row = $statusResult->fetch_assoc()) {
    $orderStatusData[$row['order_status']] = $row['total'];
}






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

#content h3 {
    color: #575f92ff; /* one of your gradient colors */
    font-weight: bold;
}

.dashboard-row {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 30px;
}

.dashboard-card {
    background: linear-gradient(150deg, #807777ff, #575f92ff);
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
.status-rings {
    display: flex;
    justify-content: center;
    gap: 50px;
    flex-wrap: wrap;
    margin-bottom: 60px;
}

.ring {
    position: relative;
    width: 140px;
    height: 160px;
    text-align: center;
}

.ring canvas {
    width: 140px !important;
    height: 140px !important;
}

.ring strong {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -60%);
    color: #ffffff; /* FIXED */
    font-size: 26px;
    font-weight: bold;
}


.ring span {
    display: block;
    margin-top: 10px;
    font-size: 14px;
    color: #575f92ff;
    text-transform: uppercase;
    letter-spacing: 1px;
}


</style>

</head>
<body>
    <div id="wrapper">
    <?php include "../php/sidebar.php"; ?>

   
</div>

<div id="content" class="container-fluid">

    <h3 class="text-center mt-4 mb-5">
        Welcome, <?= htmlspecialchars($admin['user_name']) ?>!
    </h3>
    <br>

    <div class="dashboard-row">

        <!-- Users -->
      <a href="user-html.php" style="text-decoration:none;">
    <div class="dashboard-card">
        <i class="fa fa-users"></i>
        <h4>Users</h4>
        <p><?= $userCount ?></p>
    </div>
</a>


        <!-- Orders -->
      <a href="orders-html.php" style="text-decoration:none;">
    <div class="dashboard-card">
        <i class="fa fa-box"></i>
        <h4>Orders</h4>
        <p><?= $orderCount ?></p>
    </div>
</a>


        <!-- Products -->
       <a href="product-html.php" style="text-decoration:none;">
    <div class="dashboard-card">
        <i class="fa fa-shirt"></i>
        <h4>Products</h4>
        <p><?= $productCount ?></p>
    </div>
</a>


       <a href="orders-html.php" style="text-decoration:none;">
    <div class="dashboard-card">
        <i class="fa fa-dollar-sign"></i>
        <h4>Total Revenue</h4>
        <p>$<?= number_format($totalRevenue, 2) ?></p>
    </div>
</a>


    </div>
    <br>
    <br>
    


   <h3 class="text-center mt-5 mb-4" style="#807777ff;">
    Order Status Overview
</h3>
<br>

<div class="status-rings">

<?php
$statusColors = [
    'Pending'    => '   #575f92ff',
    'Processing' => ' #575f92ff',
    'Shipped'    => ' #575f92ff',
    'Delivered'  => ' #575f92ff',
    'Cancelled'  => ' #575f92ff'
];

foreach ($orderStatusData as $status => $count):
    $color = $statusColors[$status] ?? '#807777ff';
?>
    <div class="ring">
        <canvas id="ring_<?= $status ?>"></canvas>
        <strong><?= $count ?></strong>
        <span><?= ucfirst($status) ?></span>
    </div>
<?php endforeach; ?>

</div>

</div>

</div>


</div>

</div>

<!-- Bootstrap JS -->
 <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
<?php foreach ($orderStatusData as $status => $count): ?>
(() => {
    const canvas = document.getElementById("ring_<?= $status ?>");
    const ctx = canvas.getContext("2d");

    // Create gradient (THIS is the key)
    const gradient = ctx.createLinearGradient(0, 0, 140, 140);
    gradient.addColorStop(0, "#807777ff");
    gradient.addColorStop(1, "#575f92ff");

    new Chart(ctx, {
        type: "doughnut",
        data: {
            datasets: [{
                data: [<?= $count ?>, <?= max($orderCount - $count, 0) ?>],
                backgroundColor: [
                    gradient,
                    "#e6e6e6"
                ],
                borderWidth: 0
            }]
        },
        options: {
            cutout: "78%",
            animation: {
                duration: 1800,
                easing: "easeOutCubic"
            },
            plugins: {
                legend: { display: false },
                tooltip: { enabled: false }
            }
        }
    });
})();
<?php endforeach; ?>
</script>



</body>
</html>

