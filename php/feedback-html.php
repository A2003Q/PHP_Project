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
.page-header { background-color: #4f3131; color: white; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; }
.table thead { background-color: #4f3131; color: white; }
.btn-primary { background-color: #4f3131; border: none; }
.btn-primary:hover { background-color: #3e2626; }
.card { border-radius: 12px; box-shadow: 0 0 15px rgba(0,0,0,0.08); }

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
<?php while ($f = $feedbacks->fetch_assoc()): ?>
<tr>
<td><?= $f['feedback_id'] ?></td>
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

