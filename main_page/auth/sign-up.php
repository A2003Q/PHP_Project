<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up | Fashion Store</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

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
        <div class="col-md-6">
            <div class="card auth-card shadow">
                <div class="card-body p-4">
                    <h3 class="text-center auth-title mb-4">Create Account</h3>

                    <form method="POST" action="signup.php">
                        <div class="row">
                  <div class="col-md-6 mb-3">
    <label class="form-label">First Name</label>
    <input type="text" class="form-control" id="username" required>
    <div class="invalid-feedback"></div>
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Last Name</label>
    <input type="text" class="form-control" id="lastname" required>
    <div class="invalid-feedback"></div>
</div>
</div>
<div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" class="form-control" id="email" placeholder="you@example.com" required>
    <div class="invalid-feedback"></div>
</div>

<div class="mb-3">
    <label class="form-label">Password</label>
    <input type="password" class="form-control" id="password" placeholder="Minimum 8 characters" required>
    <div class="invalid-feedback"></div>
</div>

<div class="mb-3">
    <label class="form-label">Confirm Password</label>
    <input type="password" class="form-control" id="retype_password" required>
    <div class="invalid-feedback"></div>
</div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-main">Sign Up</button>
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
