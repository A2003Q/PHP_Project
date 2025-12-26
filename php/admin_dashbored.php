<?php
session_start();
require_once "../php/Admin.php";

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

    <div id="wrapper">
    <?php include "../php/sidebar.php"; ?>

   
</div>

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

