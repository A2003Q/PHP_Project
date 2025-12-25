function showError(message) {
    Swal.fire({
        icon: "error",
        title: "Login Failed",
        text: message
    });
}

// Login form validation
document.getElementById("loginForm").addEventListener("submit", function(e) {
    e.preventDefault();

    let email = document.getElementById("email").value.trim();
    let password = document.getElementById("password").value.trim();

    // Empty field check
    if (!email || !password) {
        showError("Please enter both email and password.");
        return;
    }

    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
    if (!emailRegex.test(email)) {
        showError("Please enter a valid email address.");
        return;
    }

    // Password validation (6-20 chars, at least one letter & number)
    const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,20}$/;
    if (!passwordRegex.test(password)) {
        showError("Password must be 6-20 characters and include letters and numbers.");
        return;
    }

  

    // If all passes, submit the form
    this.submit();
});