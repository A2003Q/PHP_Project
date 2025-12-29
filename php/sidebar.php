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
/* Reset */
html, body {
    height: 100%;
    margin: 0;
    font-family: 'Nunito', sans-serif;
}

/* Wrapper */
#wrapper {
    display: flex;
    min-height: 100vh;
    align-items: stretch; /* IMPORTANT */
}

/* Sidebar */
/* Sidebar */
.sidebar {
    min-width: 250px;
    max-width: 250px;
    background: linear-gradient(180deg, #1f1f2e, #3a3a5e); /* Dark stylish gradient */
    color: white;
    display: flex;
    flex-direction: column;
    transition: all 0.3s ease;
    box-shadow: 2px 0 12px rgba(0,0,0,0.2);
}

/* Sidebar brand */
.sidebar .sidebar-brand {
    padding: 1.5rem;
    font-size: 1.5rem;
    font-weight: 700;
    text-align: center;
    letter-spacing: 1px;
    background-color: rgba(255,255,255,0.05);
    border-bottom: 1px solid rgba(255,255,255,0.2);
}

/* Sidebar links */
.sidebar a {
    color: #f0f0f5;
    text-decoration: none;
    display: flex;
    align-items: center;
    padding: 0.9rem 1.5rem;
    font-size: 1rem;
    border-radius: 8px;
    margin: 0.3rem 0;
    transition: 0.3s;
}

.sidebar a i {
    margin-right: 10px;
    font-size: 1.2rem;
}

/* Hover effect */
.sidebar a:hover {
    background-color: #5a5a7a; /* soft muted highlight */
    transform: translateX(5px);
}

/* Active link */
.sidebar .nav-item.active a {
    background-color: #7a78a0; /* slightly stronger highlight */
    font-weight: 600;
}

/* Divider */
.sidebar hr {
    border-color: rgba(255,255,255,0.2);
    margin: 1rem 0;
}

/* Content area */
#content {
    flex-grow: 1;
    padding: 2rem;
    background-color: #f8f7fa; /* soft light background for contrast */
    transition: all 0.3s ease;
}


/* Small badges */
.status-badge {
    border-radius: 20px;
    padding: 5px 12px;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
}

/* Mobile tweaks */
@media (max-width: 768px) {
    .sidebar {
        min-width: 200px;
        max-width: 200px;
    }
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