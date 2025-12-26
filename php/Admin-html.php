<?php
session_start();
require_once "../php/Admin.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$adminId = $_SESSION['user_id'];
$admin = new Admin();

$result = $admin->getAdminById($adminId);
$data = $result->fetch_assoc();

$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name     = trim($_POST['user_name']);
    $email    = trim($_POST['user_email']);
    $phone    = trim($_POST['user_phone']);
    $password = trim($_POST['user_password']);

    $success = $admin->updateAdmin(
        $adminId,
        $name,
        $email,
        $phone,
        $password // optional
    );

    // Reload updated data
    $result = $admin->getAdminById($adminId);
    $data = $result->fetch_assoc();
}
?>






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Profile</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background: linear-gradient(135deg, #988d76ff, #4f3131);
            font-family: 'Poppins', sans-serif;
        }
        .profile-card {
            max-width: 500px;
            background: #fff;
            padding: 30px;
            margin: 60px auto;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,.2);
        }
        .profile-icon {
            width: 90px;
            height: 90px;
            background:#4f3131;
            color: #fff;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 40px;
            margin: auto;
        }
        .btn-edit {
            background: #4f3131;
            color: #fff;
        }
        .btn-edit:hover {
            background: #000000ff;
        }
    </style>
</head>

<body>

<div class="profile-card text-center">

    <div class="profile-icon mb-3">
        <i class="fa-solid fa-user-shield"></i>
    </div>

    <h4><?= htmlspecialchars($data['user_name']) ?></h4>
    <p class="text-muted">Administrator</p>

   <form method="POST" class="text-start mt-4">

    <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input type="text"
               class="form-control"
               name="user_name"
               value="<?= htmlspecialchars($data['user_name']) ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email"
               class="form-control"
               name="user_email"
               value="<?= htmlspecialchars($data['user_email']) ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Phone</label>
        <input type="text"
               class="form-control"
               name="user_phone"
               value="<?= htmlspecialchars($data['user_phone']) ?>">
    </div>

    <div class="mb-4">
        <label class="form-label">New Password</label>
        <input type="password"
               class="form-control"
               name="user_password"
               placeholder="Leave empty to keep current password">
        <small class="text-muted">
            Only fill this if you want to change your password
        </small>
    </div>

    <button type="submit" class="btn btn-edit w-100">
        <i class="fa-solid fa-pen-to-square"></i> Save Changes
    </button>

</form>


</div>
<?php if (isset($success) && $success): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
Swal.fire({
    icon: 'success',
    title: 'Saved Successfully!',
    text: 'Your profile has been updated.',
    confirmButtonColor: '#4f3131'
}).then(() => {
    window.location.href = "admin_dashbored.php";
});
</script>
<?php endif; ?>


</body>
</html>

