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

  $errors = [];


  $name = trim($_POST['user_name']); $email = trim($_POST['user_email']); $phone = trim($_POST['user_phone']); $password = trim($_POST['user_password']);
// Name validation (same rule as JS)
if (!preg_match('/^[A-Za-z\x{0600}-\x{06FF} ]{2,30}$/u', $name)) {
    $errors['user_name'] = "Name must be letters only (2–30 chars).";
}

// Email validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['user_email'] = "Invalid email address.";
}

// Phone validation
if (!preg_match('/^\+?[0-9\s-]{7,20}$/', $phone)) {
    $errors['user_phone'] = "Invalid phone number.";
}

// Password validation (optional)
if ($password !== "" && !preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d!@#$%^&*()_+]{6,20}$/', $password)) {
    $errors['user_password'] = "Password must be 6–20 chars with letters & numbers.";
}


 if (empty($errors)) {
    $success = $admin->updateAdmin(
        $adminId,
        $name,
        $email,
        $phone,
        $password
    );
}


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
          
           background-color: #d8d0d0ff;
            font-family: 'Poppins', sans-serif;
        }
        .profile-card {
            max-width: 500px;
             background: linear-gradient(150deg, #807777ff, #575f92ff);
            padding: 30px;
            margin: 60px auto;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,.2);
            color:#fff;
        }
        .profile-icon {
            width: 90px;
            height: 90px;
            
            color: #fff;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 40px;
            margin: auto;
        }
        .btn-edit {
            background:  #d8d0d0ff;
            color: #fff;
        }
        .btn-edit:hover {
          background: linear-gradient(150deg, #807777ff, #575f92ff);
        }
        /* === Override Bootstrap validation colors === */

/* Invalid input border */
.form-control.is-invalid {
    border-color: #ffffff !important;
    background-image: none;
}

/* Valid input border */
.form-control.is-valid {
    border-color: #ffffff !important;
    background-image: none;
}

/* Error message text */
.invalid-feedback {
    color: #ffffff !important;
    font-size: 13px;
}

/* Optional: valid state glow */
.form-control.is-valid:focus {
    box-shadow: 0 0 0 0.15rem ;
}

/* Invalid state glow */
.form-control.is-invalid:focus {
    box-shadow: 0 0 0 0.15rem rgba(255,255,255,.35);
} 

.text-muted{
    color:#ffffff !important;
}

    </style>
</head>

<body>

<div class="profile-card text-center">

    <div class="profile-icon mb-3">
         <i class="fa-solid fa-user-shield me-2" ></i>
    </div>

    <h4><?= htmlspecialchars($data['user_name']) ?></h4>

    <p class="text-muted">Admin Profile</p>
    

   <form method="POST" class="text-start mt-4">

   <div class="mb-3">
    <label class="form-label">Username</label>
  <input type="text"
       name="user_name"
       class="form-control <?= isset($errors['user_name']) ? 'is-invalid' : '' ?>"
       value="<?= htmlspecialchars($data['user_name']) ?>">

<div class="invalid-feedback">
    <?= $errors['user_name'] ?? '' ?>
</div>
</div>


    <div class="mb-3">
    <label class="form-label">Email</label>
    <input type="text"
       name="user_email"
       class="form-control <?= isset($errors['user_email']) ? 'is-invalid' : '' ?>"
       value="<?= htmlspecialchars($data['user_email']) ?>">

<div class="invalid-feedback">
    <?= $errors['user_email'] ?? '' ?>
</div>
</div>


 <div class="mb-3">
    <label class="form-label">Phone</label>
    <input type="text"
       name="user_phone"
       class="form-control <?= isset($errors['user_phone']) ? 'is-invalid' : '' ?>"
       value="<?= htmlspecialchars($data['user_phone']) ?>">

<div class="invalid-feedback">
    <?= $errors['user_phone'] ?? '' ?>
</div>
</div>


   <div class="mb-4">
    <label class="form-label">New Password</label>
<input type="password"
       name="user_password"
       placeholder="Leave empty to keep current password"
       class="form-control <?= isset($errors['user_password']) ? 'is-invalid' : '' ?>">
      

<div class="invalid-feedback">
    <?= $errors['user_password'] ?? '' ?>
</div>
              
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
    confirmButtonColor: '#807777ff'
}).then(() => {
    window.location.href = "admin_dashbored.php";
});
</script>
<?php endif; ?>
<script>
// Validation regex
const usernameRegex = /^[A-Za-z\u0600-\u06FF ]{2,30}$/;
const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d!@#$%^&*()_+]{6,20}$/;
const phoneRegex = /^\+?[0-9\s-]{7,20}$/;

// Fields
const usernameField = document.querySelector("[name='user_name']");
const emailField    = document.querySelector("[name='user_email']");
const phoneField    = document.querySelector("[name='user_phone']");
const passwordField = document.querySelector("[name='user_password']");

// Helper for inline error
function validateField(field, regex, message) {
    field.addEventListener('input', () => {
        const val = field.value.trim();

        // Password is optional
        if (field === passwordField && val === "") {
            field.classList.remove('is-invalid', 'is-valid');
            field.nextElementSibling.textContent = '';
            return;
        }

        if (!regex.test(val)) {
            field.classList.add('is-invalid');
            field.classList.remove('is-valid');
            field.nextElementSibling.textContent = message;
        } else {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            field.nextElementSibling.textContent = '';
        }
    });
}

// Apply real-time validation
validateField(usernameField, usernameRegex, 'Username must be letters only, 2-30 chars.');
validateField(emailField, emailRegex, 'Invalid email address.');
validateField(phoneField, phoneRegex, 'Phone must be 7-20 digits, can include +, spaces, or dashes.');
validateField(passwordField, passwordRegex, 'Password 6-20 chars, letters & numbers.');

// Optional: prevent submit if invalid client-side
document.querySelector('form').addEventListener('submit', (e) => {
    const fields = [usernameField, emailField, phoneField, passwordField];
    let hasError = false;

    fields.forEach(field => {
        const val = field.value.trim();
        if (field === passwordField && val === "") return;
        const regex = field === usernameField ? usernameRegex :
                      field === emailField ? emailRegex :
                      field === phoneField ? phoneRegex :
                      passwordRegex;
        if (!regex.test(val)) {
            field.classList.add('is-invalid');
            field.classList.remove('is-valid');
            hasError = true;
        } else {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
        }
    });

    if (hasError) {
        e.preventDefault(); // prevent form submit if client-side invalid
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Please fix the errors in the form.',
            confirmButtonColor: '#807777ff'
        });
    }
});
</script>
<script>

<?php if (!empty($errors)): ?>

let errorMessages = <?php
    // Combine all errors into one string for SweetAlert
    echo json_encode(implode("\n", $errors));
?>;

Swal.fire({
    icon: 'error',
    title: 'Validation Error',
    html: errorMessages.replace(/\n/g, "<br>"),
    confirmButtonColor: '#807777ff'
});
</script>
<?php endif; ?>





</body>
</html>

