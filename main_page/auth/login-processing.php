<?php
session_start();
require_once '../../SQL/Database.php';


$conn = Database::getInstance()->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        showAlertAndRedirect('warning', 'Fields Required', 'All fields are required');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        showAlertAndRedirect('error', 'Invalid Email', 'Please enter a valid email address');
    }

    $stmt = $conn->prepare(
        "SELECT user_id, user_name, user_password, user_role
         FROM users
         WHERE user_email = ?"
    );
    
    if (!$stmt) {
        showAlertAndRedirect('error', 'Database Error', 'Failed to prepare statement: ' . $conn->error);
    }
    
    $stmt->bind_param("s", $email);
    
    if (!$stmt->execute()) {
        showAlertAndRedirect('error', 'Database Error', 'Failed to execute query: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        showAlertAndRedirect('error', 'Login Failed', 'Invalid email or password');
    }

    $user = $result->fetch_assoc();

    if (!password_verify($password, $user['user_password'])) {
        showAlertAndRedirect('error', 'Login Failed', 'Invalid email or password');
    }

    $_SESSION['user_id']   = $user['user_id'];
    $_SESSION['username']  = $user['user_name']; 
    $_SESSION['role']      = $user['user_role'];
    $_SESSION['logged_in'] = true;

    $redirect = ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'Admin') 
                ? '../../php/admin_dashbored.php' 
                : '../index.php';

    showAlertAndRedirect('success', 'Login Successful', 'Welcome back, ' . htmlspecialchars($user['user_name']) . '!', $redirect);
}

function showAlertAndRedirect($type, $title, $message, $redirectUrl = null) {
    $html = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . htmlspecialchars($title) . '</title>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: "' . $type . '",
                title: "' . addslashes($title) . '",
                text: "' . addslashes($message) . '",
                confirmButtonText: "OK"
            }).then(() => {';
    
    if ($redirectUrl) {
        $html .= 'window.location.href = "' . $redirectUrl . '";';
    } else {
        $html .= 'window.history.back();';
    }
    
    $html .= '});
        </script>
    </body>
    </html>';
    
    echo $html;
    exit;
}
?>