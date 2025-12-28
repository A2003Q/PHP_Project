<?php
// optional: protect admin pages
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
      html, body {
    height: 100%;
}

body {
    font-family: 'Nunito', sans-serif;
    margin: 0;
}

#wrapper {
    display: flex;
    align-items: stretch; /* IMPORTANT */
    min-height: 100vh;
}

/* Sidebar */
.sidebar {
    min-width: 220px;
    max-width: 220px;
     background: linear-gradient(150deg, #807777ff, #575f92ff);
    color: white;
}

/* Sidebar links */
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
    background-color:  #b2b2b2;
}

/* Content */
#content {
    flex-grow: 1;
    padding: 2rem;
    background-color: #d8d0d0ff;
}

    </style>
</head>
<body>

<div id="wrapper">
    <!-- Sidebar -->
    <ul class="sidebar navbar-nav">
        <!-- Brand -->
        <a class="sidebar-brand" href="admin_dashbored.php">
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
    <a class="nav-link" href="orders-html.php">
        <i class="fa-solid fa-cart-shopping me-2"></i>
        <span>Orders</span>
    </a>
</li>

<!-- Categories Management -->
<li class="nav-item">
    <a class="nav-link" href="categories-html.php">
        <i class="fa-solid fa-tags me-2"></i>
        <span>Categories</span>
    </a>
</li>
<!-- FeedBack Management -->
<li class="nav-item">
    <a class="nav-link" href="feedback-html.php">
        <i class="fa-solid fa-comment"></i>
        <span>FeedBack</span>
    </a>
</li>


        <hr class="sidebar-divider">

        <!-- Logout -->
        <li class="nav-item">
            <a class="nav-link" href="../main_page/auth/login.php">
                <i class="fa-solid fa-right-from-bracket me-2"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>