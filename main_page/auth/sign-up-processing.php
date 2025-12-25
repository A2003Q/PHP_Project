<?php 
session_start();
require_once '../../SQL/Database.php'; 


$conn = Database::getInstance()->getConnection();
if(isset($_POST['signup'])) {
   $name= $_POST['firstname'] . ' ' . $_POST['lastname'];   
    $username = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $email    = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');
    $retype   = htmlspecialchars($_POST['retypepassword'], ENT_QUOTES, 'UTF-8');
    $phone    = htmlspecialchars($_POST['phone'], ENT_QUOTES, 'UTF-8');
    $role     = 'user';
    if($password !== $retype) {
        echo "Passwords do not match!";
        exit;
    }
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email";
    exit;
}

if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d).{6,}$/', $password)) {
    exit("Password must contain letters and numbers");
}
    
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if user already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_name=? OR user_email=?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        echo "Username or email already taken!";
        exit;
    }

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (user_name, user_email, user_password, user_role,user_phone) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $username, $email, $hashedPassword,$role, $phone);

    if($stmt->execute()) {
       
      
        header("Location: login.php");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>












?>