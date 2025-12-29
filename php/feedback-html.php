<?php
require_once "../php/feedback.php";
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}


$feed = new Feedback();
$feedbacks = $feed->getAllFeedback();
$alert = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'delete') {
    $feed->deleteFeedback($_POST['id']);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>FeedBack</title>

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
    <h3>Feedback Management</h3>
</div>



<div class="card p-3">
<table class="table table-bordered">
<thead class="table-dark">
<tr>
    <th>ID</th><th>Comment</th><th>Rating</th><th>User_id</th><th>product_id</th><th>Actions</th>
</tr>
</thead>
<tbody>
    <?php $i=1 ?>
<?php while ($f = $feedbacks->fetch_assoc()): ?>
<tr>
<td><?= $i++ ?></td>
<td><?= htmlspecialchars($f['feedback_comment']) ?></td>
<td><?= htmlspecialchars($f['feedback_rating']) ?></td>
<td><?= htmlspecialchars($f['user_id']) ?></td>
<td><?= htmlspecialchars($f['product_id']) ?></td>
<td>

<form method="POST" class="d-inline">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" value="<?= $f['feedback_id'] ?>">
    <button class="btn btn-danger btn-sm">
        <i class="fa fa-trash"></i>
    </button>
</form>

</td>
</tr>
<?php endwhile; ?>
<?php if ($alert): ?>
<script>
Swal.fire({
icon: 'success',
title: 'Success',
text: 'Action completed successfully'
}).then(()=>location.href='admin_dashbored.php');
</script>
<?php endif; ?>
</tbody>
</table>
</div>

