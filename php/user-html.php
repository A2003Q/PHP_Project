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
    try {
        $success = $usersClass->addUser(
            trim($_POST['name']),
            trim($_POST['email']),
            trim($_POST['phone']),
            trim($_POST['password'])
        );
        if ($success) $alert = "added";
        else $alert = "email_exists"; // add custom alert
    } catch (Exception $e) {
        $alert = "email_exists"; // in case your addUser throws exception
    }
}

/* ---------- EDIT USER ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit') {
    try {
        $success = $usersClass->updateUser(
            $_POST['id'],
            trim($_POST['name']),
            trim($_POST['email']),
            trim($_POST['phone']),
            trim($_POST['password'])
        );
        if ($success) $alert = "updated";
        else $alert = "email_exists";
    } catch (Exception $e) {
        $alert = "email_exists";
    }
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
.page-header {  
      background: linear-gradient(180deg, #1f1f2e, #3a3a5e);
    color: white; 
    padding: 15px 20px; 
    border-radius: 8px; 
    margin-bottom: 20px; 
}
.table thead {
       background: linear-gradient(180deg, #1f1f2e, #3a3a5e); 
    color: #fff;
}
.table thead th {
    background: linear-gradient(180deg, #1f1f2e, #3a3a5e); 
      color: #fff;
}

/* Add User Button */
.btn-success {
 background: linear-gradient(180deg, #1f1f2e, #3a3a5e);
    border: none;
    color: #fff;
}
.btn-success:hover {
   background: linear-gradient(180deg, #1f1f2e, #3a3a5e);
}

/* Action buttons (Edit & Delete) */
.btn-primary {
     background: linear-gradient(180deg, #1f1f2e, #3a3a5e); 
    border: none; /* remove edit border */
    color: #fff;
}
.btn-primary:hover {
    background: linear-gradient(180deg, #1f1f2e, #3a3a5e);
}
.btn-danger {
    background: linear-gradient(180deg, #1f1f2e, #3a3a5e);
    border: 1px solid #fff; /* keep delete border */
    color: #fff;
}
.btn-danger:hover {
     background: linear-gradient(180deg, #1f1f2e, #3a3a5e);
}

/* Card */
.card { 
    border-radius: 12px; 
    box-shadow: 0 0 15px rgba(0,0,0,0.08); 
}

/* Modal Styling */
.modal-dialog {
    max-width: 600px; /* bigger modal */
    margin: 10% auto; /* center vertically */
}
.modal-content {
    border-radius: 12px;
     background: linear-gradient(180deg, #1f1f2e, #3a3a5e);
    color: #fff;
}
.modal-body .form-control {
    background-color: rgba(255,255,255,0.1);
    border: 1px solid #fff;
    color: #fff;
}
.modal-body .form-control::placeholder {
    color: #ddd;
}
.modal-body .form-control:focus {
    background-color: rgba(255,255,255,0.15);
    color: #fff;
    border-color: #fff;
    box-shadow: none;
}
.modal-footer .btn {
    color: #fff;
    border: none;
}

/* Inline validation styles */
.form-control.is-invalid {
    border-color: #ffffff !important;
    background-image: none;
}
.form-control.is-valid {
    border-color: #ffffff !important;
    background-image: none;
}
.invalid-feedback {
    color: #ffffff !important;
    font-size: 13px;
}
</style>
</head>
<body>
<div id="wrapper">
    <?php include "../php/sidebar.php"; ?>
</div>

<div id="content">
<div class="page-header">
    <h3>User Management</h3>
</div>

<button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addUserModal">
    <i class="fa fa-plus"></i> Add User
</button>

<div class="card p-3">
<table class="table table-bordered">
<thead class="table-dark">
<tr>
    <th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Actions</th>
</tr>
</thead>
<tbody>
    <?php $i = 1; ?>
<?php while ($user = $users->fetch_assoc()): ?>
<tr>
<td><?= $i++ ?></td>
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
<form method="POST" autocomplete="off">
<input type="hidden" name="action" value="add">
<div class="modal-content">
<div class="modal-body">
<input name="name" class="form-control mb-2" placeholder="Name" required>
<div class="invalid-feedback"></div>
<input
  name="email"
  id="addEmail"
  class="form-control mb-2"
  placeholder="Email"
  autocomplete="off"
  required
>
<div class="invalid-feedback"id="addEmailError"></div>
<input name="phone" class="form-control mb-2" placeholder="Phone">
<div class="invalid-feedback"></div>
<input
  name="password"
  type="password"
  class="form-control mb-2"
  placeholder="Password"
  autocomplete="new-password"
  required
>
<div class="invalid-feedback"></div>
</div>
<div class="modal-footer">
<button class="btn btn-success w-100">Add</button>
</div>
</div>
</form>
</div>
</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="editUserModal">
<div class="modal-dialog">
<form method="POST" >
<input type="hidden" name="action" value="edit">
<input type="hidden" name="id" id="editUserId">
<div class="modal-content">
<div class="modal-body">
<input name="name" id="editUserName" class="form-control mb-2" required>
<div class="invalid-feedback"></div>
<input name="email" id="editUserEmail" class="form-control mb-2" required>
<div class="invalid-feedback"id="editEmailError"></div>
<input name="phone" id="editUserPhone" class="form-control mb-2">

<input name="password" type="password" class="form-control mb-2" placeholder="Leave empty to keep">
<div class="invalid-feedback"></div>
</div>
<div class="modal-footer">
<button class="btn btn-primary w-100">Update</button>
</div>
</div>
</form>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Edit Button
document.querySelectorAll('.editBtn').forEach(btn => {
    btn.onclick = () => {
        editUserId.value = btn.dataset.id;
        editUserName.value = btn.dataset.name;
        editUserEmail.value = btn.dataset.email;
        editUserPhone.value = btn.dataset.phone;
        new bootstrap.Modal(editUserModal).show();
    };
});

// Delete Button with SweetAlert
document.querySelectorAll('.deleteBtn').forEach(btn => {
    btn.onclick = () => {
        const id = btn.dataset.id;  
        Swal.fire({
            title: 'Delete user?',
            text: 'This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1f1f2e',
            confirmButtonText: 'Yes, delete'
        }).then(res => {
            if (res.isConfirmed) {
                window.location.href = `user-html.php?delete_id=${id}`;
            }
        });
    };
});

document.querySelectorAll('.form-control').forEach(input => {
    input.addEventListener('input', () => {
        const val = input.value.trim();
        const feedback = input.nextElementSibling;

        if (input.name === 'phone' && val !== '' && !phoneRegex.test(val)) {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
            feedback.textContent = 'Invalid Jordanian phone number';
        } 
        else if (input.name === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(val)) {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
            feedback.textContent = 'Invalid email format';
        }
        else {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            feedback.textContent = '';
        }
    });
});


</script>

<?php if ($alert): ?>
<script>
Swal.fire({
icon: 'success',
title: 'Success',
text: 'Action completed successfully'
}).then(()=>location.href='user-html.php');
</script>
<?php endif; ?>

<?php if ($alert === 'email_exists'): ?>
<script>
const addEmail = document.getElementById('addEmail');
const editEmail = document.getElementById('editEmail');

if (addEmail) {
    addEmail.classList.add('is-invalid');
    document.getElementById('addEmailError').textContent =
        'This email is already used. Please choose another one.';
}

if (editEmail) {
    editEmail.classList.add('is-invalid');
    document.getElementById('editEmailError').textContent =
        'This email is already used. Please choose another one.';
}
</script>
<?php endif; ?>

</body>
</html>

