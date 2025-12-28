<?php 
session_start();
require_once '../../SQL/Database.php';

echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>';

$conn = Database::getInstance()->getConnection();

if(isset($_POST['signup'])) {
   $name = $_POST['firstname'] . ' ' . $_POST['lastname'];   
    $username = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $email    = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');
    $retype   = htmlspecialchars($_POST['retypepassword'], ENT_QUOTES, 'UTF-8');
    $phone    = htmlspecialchars($_POST['phone'], ENT_QUOTES, 'UTF-8');
    $role     = 'user';
    
    if($password !== $retype) {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "Password Mismatch",
                text: "Passwords do not match!",
                confirmButtonText: "Try Again"
            }).then(() => {
                window.history.back();
            });
        </script>';
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "Invalid Email",
                text: "Please enter a valid email address",
                confirmButtonText: "Try Again"
            }).then(() => {
                window.history.back();
            });
        </script>';
        exit;
    }

    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d).{6,}$/', $password)) {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "Weak Password",
                text: "Password must contain letters and numbers, minimum 6 characters",
                confirmButtonText: "Try Again"
            }).then(() => {
                window.history.back();
            });
        </script>';
        exit;
    }
    
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT * FROM users WHERE user_name=? OR user_email=?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        echo '<script>
            Swal.fire({
                icon: "warning",
                title: "Already Taken",
                text: "Username or email already taken!",
                confirmButtonText: "Try Again"
            }).then(() => {
                window.history.back();
            });
        </script>';
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO users (user_name, user_email, user_password, user_role, user_phone) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $username, $email, $hashedPassword, $role, $phone);

    if($stmt->execute()) {
        echo '<script>
            Swal.fire({
                icon: "success",
                title: "Registration Successful",
                text: "Your account has been created successfully!",
                confirmButtonText: "Continue to Login"
            }).then(() => {
                window.location.href = "login.php";
            });
        </script>';
        exit;
    } else {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "Registration Failed",
                text: "Error: ' . addslashes($conn->error) . '",
                confirmButtonText: "Try Again"
            }).then(() => {
                window.history.back();
            });
        </script>';
        exit;
    }
}

echo '</body></html>';
?>