<?php
session_start();
require_once "../php/users.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$usersClass = new Users();
$alert = null;

/* ---------- ADD USER ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $success = $usersClass->addUser(
        trim($_POST['name']),
        trim($_POST['email']),
        trim($_POST['phone']),
        trim($_POST['password'])
    );
    if ($success) $alert = "added";
}

/* ---------- EDIT USER ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit') {
    $success = $usersClass->updateUser(
        $_POST['id'],
        trim($_POST['name']),
        trim($_POST['email']),
        trim($_POST['phone']),
        trim($_POST['password'])
    );
    if ($success) $alert = "updated";
}

/* ---------- DELETE USER ---------- */
if (isset($_GET['delete_id'])) {
    if ($usersClass->deleteUser($_GET['delete_id'])) {
        $alert = "deleted";
    }
}

$users = $usersClass->getAllUsers();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Users</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


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
    <a class="nav-link" href="user.php">
        <i class="fa-solid fa-users me-2"></i>
        <span>User Management</span>
    </a>
</li>

<!-- Products Management -->
<li class="nav-item">
    <a class="nav-link" href="products.php">
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
    <!-- End of Sidebar -->
</head>
<body>
    <div class="container mt-4">
<h2>User Management</h2>


<button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addUserModal">
    <i class="fa fa-plus"></i> Add User
</button>

<table class="table table-bordered">
<thead class="table-dark">
<tr>
    <th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Actions</th>
</tr>
</thead>
<tbody>
<?php while ($user = $users->fetch_assoc()): ?>
<tr>
<td><?= $user['user_id'] ?></td>
<td><?= htmlspecialchars($user['user_name']) ?></td>
<td><?= htmlspecialchars($user['user_email']) ?></td>
<td><?= htmlspecialchars($user['user_phone']) ?></td>
<td>
<button class="btn btn-primary btn-sm editBtn"
        data-id="<?= $user['user_id'] ?>"
        data-name="<?= htmlspecialchars($user['user_name']) ?>"
        data-email="<?= htmlspecialchars($user['user_email']) ?>"
        data-phone="<?= htmlspecialchars($user['user_phone']) ?>">
<i class="fa fa-edit"></i>
</button>

<button class="btn btn-danger btn-sm deleteBtn"
        data-id="<?= $user['user_id'] ?>">
<i class="fa fa-trash"></i>
</button>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>

<!-- ADD MODAL -->
<div class="modal fade" id="addUserModal">
<div class="modal-dialog">
<form method="POST">
<input type="hidden" name="action" value="add">
<div class="modal-content">
<div class="modal-body">
<input name="name" class="form-control mb-2" placeholder="Name" required>
<input name="email" class="form-control mb-2" placeholder="Email" required>
<input name="phone" class="form-control mb-2" placeholder="Phone">
<input name="password" type="password" class="form-control mb-2" placeholder="Password" required>
</div>
<button class="btn btn-success">Add</button>
</div>
</form>
</div>
</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="editUserModal">
<div class="modal-dialog">
<form method="POST">
<input type="hidden" name="action" value="edit">
<input type="hidden" name="id" id="editUserId">
<div class="modal-content">
<div class="modal-body">
<input name="name" id="editUserName" class="form-control mb-2" required>
<input name="email" id="editUserEmail" class="form-control mb-2" required>
<input name="phone" id="editUserPhone" class="form-control mb-2">
<input name="password" type="password" class="form-control mb-2" placeholder="Leave empty to keep">
</div>
<button class="btn btn-primary">Update</button>
</div>
</form>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.querySelectorAll('.editBtn').forEach(btn => {
    btn.onclick = () => {
        editUserId.value = btn.dataset.id;
        editUserName.value = btn.dataset.name;
        editUserEmail.value = btn.dataset.email;
        editUserPhone.value = btn.dataset.phone;
        new bootstrap.Modal(editUserModal).show();
    };
});

document.querySelectorAll('.deleteBtn').forEach(btn => {
    btn.onclick = () => {
        const id = btn.dataset.id;

        Swal.fire({
            title: 'Delete user?',
            text: 'This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete'
        }).then(res => {
            if (res.isConfirmed) {
                window.location.href = `user.php?delete_id=${id}`;
            }
        });
    };
});

</script>

<?php if ($alert): ?>
<script>
Swal.fire({
icon: 'success',
title: 'Success',
text: 'Action completed successfully'
}).then(()=>location.href='admin_dashbored.php');
</script>
<?php endif; ?>

</body>
</html>

