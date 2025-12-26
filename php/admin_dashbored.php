<?php
session_start();
require_once "../php/Admin.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../php/login.php");
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

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
        #wrapper {
            display: flex;
        }
        /* Sidebar */
        .sidebar {
            min-width: 220px;
            max-width: 220px;
            background-color: #5d2727;
            color: white;
            height: 100vh;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
        }
        .sidebar .sidebar-brand {
            padding: 1rem;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .sidebar .nav-item {
            padding: 0.5rem 1rem;
        }
        .sidebar .nav-item:hover {
            background-color: #4f3131;
        }
        /* Content */
        #content {
            flex-grow: 1;
            padding: 2rem;
            background-color:  #ffffff;
            min-height: 100vh;
        }
    </style>
</head>
<body>

<div id="wrapper">
    <!-- Sidebar -->
    <ul class="sidebar navbar-nav">
        <!-- Brand -->
        <a class="sidebar-brand" href="#">
            <i class="fa-solid fa-user-shield me-2"></i>
            Admin Dashboard
        </a>

        <hr class="sidebar-divider">

        <!-- Admin Profile -->
        <li class="nav-item">
            <a class="nav-link" href="Admin-html.php">
                <i class="fa-solid fa-id-badge me-2"></i>
                <span>Admin Profile</span>
            </a>
        </li>

        <hr class="sidebar-divider">

       <!-- Users Management -->
<li class="nav-item">
    <a class="nav-link" href="user-html.php">
        <i class="fa-solid fa-users me-2"></i>
        <span>User Management</span>
    </a>
</li>

<!-- Products Management -->
<li class="nav-item">
    <a class="nav-link" href="product-html.php">
        <i class="fa-solid fa-boxes-stacked me-2"></i>
        <span>Products</span>
    </a>
</li>

<!-- Orders Management -->
<li class="nav-item">
    <a class="nav-link" href="orders.php">
        <i class="fa-solid fa-cart-shopping me-2"></i>
        <span>Orders</span>
    </a>
</li>

<!-- Categories Management -->
<li class="nav-item">
    <a class="nav-link" href="categories.php">
        <i class="fa-solid fa-tags me-2"></i>
        <span>Categories</span>
    </a>
</li>


        <hr class="sidebar-divider">

        <!-- Logout -->
        <li class="nav-item">
            <a class="nav-link" href="../main_page/index.html">
                <i class="fa-solid fa-right-from-bracket me-2"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>

    <!-- Content Wrapper -->
    <div id="content">
        <!-- Empty top content -->
        <h2 class="text-center text-gray-700 mt-5">
    Welcome, <?= htmlspecialchars($admin['user_name']) ?>!
</h2>

    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

