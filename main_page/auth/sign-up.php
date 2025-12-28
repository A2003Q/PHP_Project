<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up | Fashion Store</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="../images/icons/favicon.png"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="../vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="../fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="../fonts/iconic/css/material-design-iconic-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="../fonts/linearicons-v1.0.0/icon-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="../vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="../vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="../vendor/animsition/css/animsition.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="../vendor/select2/select2.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="../vendor/perfect-scrollbar/perfect-scrollbar.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="../css/util.css">
	<link rel="stylesheet" type="text/css" href="../css/main.css">
    <style>
        body {
            background: #f8f9fa;
        }
        .auth-card {
            border: none;
            border-radius: 12px;
        }
        .auth-title {
            font-weight: 700;
        }
        .btn-main {
            background-color: #000;
            color: #fff;
        }
        .btn-main:hover {
            background-color: #222;
        }
    </style>
</head>
<body>


<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6">
            <div class="card auth-card shadow">
                <div class="card-body p-4">
                    <h3 class="text-center auth-title mb-4">Create Account</h3>

               <form method="POST" action="sign-up-processing.php" id="signupForm">
  <div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">First Name</label>
        <input type="text" class="form-control" id="firstname" name="firstname" required>
        <div class="invalid-feedback"></div>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Last Name</label>
        <input type="text" class="form-control" id="lastname" name="lastname" required>
        <div class="invalid-feedback"></div>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" class="form-control" id="email" name="email" placeholder="you@example.com" required>
    <div class="invalid-feedback"></div>
</div>

<div class="mb-3">
    <label class="form-label">Password</label>
    <input type="password" class="form-control" id="password" name="password" placeholder="Minimum 8 characters" required>
    <div class="invalid-feedback"></div>
</div>

<div class="mb-3">
    <label class="form-label">Confirm Password</label>
    <input type="password" class="form-control" id="retype_password" name="retypepassword" required>
    <div class="invalid-feedback"></div>
</div>

<div class="mb-3">
    <label class="form-label">Phone Number</label>
    <input type="text" class="form-control" id="phone" name="phone" required>
    <div class="invalid-feedback"></div>
</div>

    <div class="d-grid mb-3">
        <button type="submit" class="btn btn-main" name="signup">Sign Up</button>
    </div>

    <p class="text-center mb-0">
        Already have an account?
        <a href="login.php">Login</a>
    </p>
</form>


                </div>
            </div>
        </div>
    </div>
</div>
<script src="sign-up.js"></script>






</body>
</html>
