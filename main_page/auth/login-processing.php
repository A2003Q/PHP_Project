<?php 

session_start();
require_once '../../SQL/Database.php';

$conn = Database::getInstance()->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        echo "All fields are required";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format";
        exit;
    }

    $stmt = $conn->prepare(
        "SELECT user_id, user_name, user_password,user_role
         FROM users
         WHERE user_email = ?"
    );
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        echo "Invalid email or password";
        exit;
    }

    $user = $result->fetch_assoc();

    if (!password_verify($password, $user['user_password'])) {
        echo "Invalid email or password";
        exit;
    }

   

    $_SESSION['user_id']   = $user['user_id'];
    $_SESSION['username']  = $user['user_name'];
    $_SESSION['role']      = $user['user_role'];
    $_SESSION['logged_in'] = true;
    if ($_SESSION['role'] === 'Admin') {
    header("Location: ../../php/admin_dashbored.php");
} else {
    header("Location: ../index.php");
    
}

    
    exit;
}













?>