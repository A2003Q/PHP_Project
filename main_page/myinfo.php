  <?php 
session_start();
require_once '../SQL/Database.php';
$conn = Database::getInstance()->getConnection();
$stmt = $conn->prepare("SELECT user_name, user_email, user_password FROM users WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc(); 
} else {
    die("User not found.");
}
  
  ?>
  
  
  <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

  
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script defer src="../JS/user_profile.js"></script>
    <style> body {
            background-color: #f5f5f5;
            /* Light grey styling outside the card */
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .profile-card {
            background-color: #3a3a5e;
            /* The dusty rose/mauve color from image */
            width: 100%;
            max-width: 900px;
            border-radius: 30px;
            padding: 50px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            position: relative;

        }

        /* Profile Header Section */
        .profile-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 50px;
        }

        .profile-icon {
            width: 70px;
            height: 70px;
            background-color: #3a3a5e;
            /* Dark grey circle background */
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 35px;
        }

        .profile-names h2 {
            color: white;
            font-size: 1.5rem;
            font-weight: 500;
            margin: 0;
            margin-bottom: 2px;
        }

        .profile-names span {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            font-weight: 300;
        }

        /* Form Styling */
        .form-label {
            color: white;
            font-size: 1.1rem;
            font-weight: 400;
            margin-bottom: 15px;
            margin-left: 10px;
            /* Slight alignment with the rounded input */
        }

        .form-control-custom {
            background-color: #ffffff;
            border: none;
            border-radius: 50px;
            /* Rounded pill shape */
            padding: 15px 30px;
            font-size: 1rem;
            color: #666;
            width: 100%;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .form-control-custom:focus {
            box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.3);
            outline: none;
        }

        /* Make read-only inputs look identical to normal but non-interactive */
        .form-control-custom:read-only {
            background-color: #ffffff;
        }

        .btn-edit {
            background-color: #ffffff;
            /* Dark reddish brown button */
            color:  #3a3a5e;
            border: none;
            border-radius: 10px;
            padding: 10px 40px;
            font-weight: 500;
            float: right;
            margin-top: 30px;
            transition: opacity 0.2s;
        }


        .btn-edit:hover {
            color: white;
            background-color: #3a3a5e;
            opacity: 0.9;
        }

         .btn-edit:active {
             
            background-color: #ff0000;
           

        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .profile-card {
                padding: 30px 20px;
                border-radius: 20px;
            }

            .profile-header {
                flex-direction: column;
                text-align: center;
                gap: 10px;
                margin-bottom: 30px;
            }

            .profile-names h2 {
                font-size: 1.3rem;
            }

            .btn-edit {
                width: 100%;
                /* Full width button on mobile */
                float: none;
            }
        }</style>
</head>

<body>
<form method="POST"  id="myinfoForm">
    <div class="profile-card">
        <!-- Header with Icon and Name -->
        <div class="profile-header">
            <div class="profile-icon">
                <i class="fa-solid fa-user"></i>
            </div>
            <div class="profile-names">
                <h2 id="topName">“Full Name”</h2>
                <span>User</span>
            </div>
        </div>

        <!-- Form Fields Grid -->
        <div class="row g-4">
            <!-- Full Name -->
            <div class="col-md-6">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-control-custom" id="username" name="username" placeholder="username" value="<?= $user['user_name'] ?>"  >
            </div>

            <!-- Email -->
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" class="form-control-custom" id="email" name="email" placeholder="email" value="<?= $user['user_email'] ?>"  >
            </div>

            <!-- Password -->
            <div class="col-md-6">
                <label class="form-label">Password</label>
                <input type="password" class="form-control-custom" id="password" placeholder="keep it empty if you dont want to change it" name="password" >
            </div>

     

        <!-- Edit Button -->
        <div class="d-flex justify-content-end w-100">
            <button class="btn btn-edit" type="submit">Edit</button>
        </div>
    </div>
    </form>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php 

if (!isset($_SESSION['user_id'])) {
    echo "<script>
            Swal.fire({
                icon: 'warning',
                title: 'Login Required',
                text: 'Please log in to access your profile.',
                confirmButtonText: 'OK'
            });
        </script>";
    exit();
}
if($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = $_POST['username'];
    $email = $_POST['email'];
    $passwordregex='/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/';
    if(!empty($_POST['password']) && !preg_match($passwordregex, $_POST['password'])) {
        echo "<script>
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Password',
                text: 'Password must be at least 8 characters long and include letters, numbers, and special characters.',
                confirmButtonText: 'OK'
            });
        </script>";
        exit();
    }
    if(!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE users SET user_name = ?, user_email = ?, user_password = ? WHERE user_id = ?");
        $stmt->bind_param("sssi", $username, $email, $password, $_SESSION['user_id']);
    } else {
        $stmt = $conn->prepare("UPDATE users SET user_name = ?, user_email = ? WHERE user_id = ?");
        $stmt->bind_param("ssi", $username, $email, $_SESSION['user_id']);
       
    }
$stmt->execute();
    echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Profile Updated',
                text: 'Your profile information has been successfully updated.',
                confirmButtonText: 'OK'
            }).then(() => {
                location.href = 'index.php';
            });
        </script>";


}
?>