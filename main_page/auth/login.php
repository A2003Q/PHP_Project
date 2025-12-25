<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Fashion Store</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

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
        <div class="col-md-5">
            <div class="card auth-card shadow">
                <div class="card-body p-4">
                    <h3 class="text-center auth-title mb-4">Welcome Back</h3>

                    <form method="POST" action="login.php" id="loginForm">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" placeholder="you@example.com" id="email" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" placeholder="••••••••" id="password" required>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-main">Login</button>
                        </div>

                        <p class="text-center mb-0">
                            Don’t have an account?
                            <a href="signup.html">Sign up</a>
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
