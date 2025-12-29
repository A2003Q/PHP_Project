<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Fashion Store</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<head>
	<title>About</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
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
<!--===============================================================================================-->
</head>
    <style>
        body {
            background: #f8f9fa;
            
        }
        .auth-card {
              background: linear-gradient(180deg, #1f1f2e, #3a3a5e);
            border: none;
            border-radius: 12px;
            color: #fff;
            height: 80vh;
            
        }
        .auth-title {
            font-weight: 700;
        }
        .btn-main {
            background-color: #000;
            color: #fff;
        }
        .btn-main:hover {
            background-color: color: #fff;
        }
    </style>
</head>
<body>


<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5">
            <div class="card auth-card shadow">
                <div class="card-body p-4">
                    <br>
                    <h3 class="text-center auth-title mb-4">Welcome Back !</h3>

                    <br>
                    <br>

                    <form method="POST" action="login-processing.php" id="loginForm" autocomplete="off">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" placeholder="you@example.com" id="email" name="email" autocomplete="off" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" placeholder="••••••••" id="password" name="password" autocomplete="new-password" required>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-main" name="login">Login</button>
                        </div>

                        <p class="text-center mb-0">
                            Don’t have an account?
                            <a href="sign-up.php">Sign up</a>
                        </p>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
<script src="login.js"></script>


</body>
</html>
