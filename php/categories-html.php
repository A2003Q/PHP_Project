<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../php/categories.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$cate = new categories();




if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    switch ($_POST['action']) {

        case 'add':
            $cate->addCategory(
                $_POST['categories_name'],
                $_POST['categories_description'],
                $_POST['categories_picture'],
            );
            break;

        case 'edit':
            $cate->updateCategory(
                $_POST['id'],
                $_POST['name'],
                $_POST['desc'],
                $_POST['pic'],
            );
            break;

        case 'delete':
            $cate->deleteCategory($_POST['id']);
            break;
        }}

 $cates= $cate->getAllCategories();

 ?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Categories</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
    <h3>Categories Management</h3>
</div>

<button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addCategoriesModal">
    <i class="fa fa-plus"></i> Add Category
</button>

<div class="card p-3">
<table class="table table-bordered">
<thead class="table-dark">
<tr>
<th>ID</th><th>categories_name</th><th>categories_description</th><th>categories_picture</th><th>Actions</th>
</tr>
</thead>
<tbody>
      <?php $i = 1; ?>
<?php while ($c = $cates->fetch_assoc()): ?>
<tr>
<td><?= $i++ ?></td>
<td><?= htmlspecialchars($c['categories_name']) ?></td>
<td><?= $c['categories_description'] ?></td>
<td><?= $c['categories_picture'] ?></td>
<td>
<button class="btn btn-primary btn-sm editBtn"
        data-id="<?= $c['categories_id'] ?>"
        data-name="<?= htmlspecialchars($c['categories_name']) ?>"
        data-desc="<?= htmlspecialchars($c['categories_description'])?>"
        data-pic="<?= htmlspecialchars($c['categories_picture']) ?>">
    <i class="fa fa-edit"></i>
</button>

<!-- DELETE FORM -->
<form method="POST" class="d-inline deleteForm">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" value="<?= $c['categories_id'] ?>">
    <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
</form>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</tbody>
</table>
</div>

<!-- ADD PRODUCT MODAL -->
<div class="modal fade" id="addCategoriesModal">
<div class="modal-dialog">
<form method="POST">
<input type="hidden" name="action" value="add">
<div class="modal-content p-3">
<input name="categories_name" class="form-control mb-2" placeholder="Name" required>
<textarea name="categories_description" class="form-control mb-2" placeholder="Description"></textarea>
<input name="categories_picture" type="text" class="form-control mb-2" placeholder="picture-url" required>
<button class="btn btn-success">Add</button>
</div>
</form>
</div>
</div>

<!-- EDIT PRODUCT MODAL -->
<div class="modal fade" id="editCategoriesModal">
<div class="modal-dialog">
<form method="POST">
<input type="hidden" name="action" value="edit">
<input type="hidden" name="id" id="editId">
<div class="modal-content p-3">
<input name="name"id="editName" class="form-control mb-2" placeholder="Name" required>
<textarea name="desc"id="editDesc" class="form-control mb-2" placeholder="Description"></textarea>
<input name="pic"id="editPic" type="text" class="form-control mb-2" placeholder="picture-url" required>
<button class="btn btn-primary">Update</button>
</div>
</form>
</div>
</div>

<script>
// EDIT PRODUCT
document.querySelectorAll('.editBtn').forEach(btn => {
    btn.onclick = () => {
        editId.value = btn.dataset.id;
        editName.value = btn.dataset.name;
        editDesc.value = btn.dataset.desc;
        editPic.value = btn.dataset.pic;
        
        new bootstrap.Modal(editCategoriesModal).show();
    };
});

// DELETE PRODUCT WITH SWEETALERT
document.querySelectorAll('.deleteForm').forEach(form => {
    form.onsubmit = e => {
        e.preventDefault();
        Swal.fire({
            title: 'Delete product?',
            icon: 'warning',
            showCancelButton: true
        }).then(res => {
            if (res.isConfirmed) form.submit();
        });
    };
});
</script>