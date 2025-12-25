// Grab form fields
const form = document.getElementById("signupForm");
const usernameField = document.getElementById("firstname");
const lastnameField = document.getElementById("lastname");
const emailField = document.getElementById("email");
const passwordField = document.getElementById("password");
const retypePasswordField = document.getElementById("retype_password");
const phoneField = document.getElementById("phone"); // new phone field

// Validation regex
const usernameRegex = /^[A-Za-z\u0600-\u06FF ]{2,30}$/;
const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d!@#$%^&*()_+]{6,20}$/;
const phoneRegex = /^\+?[0-9\s-]{7,20}$/; // phone regex

// Helper for inline error
function validateField(field, regex, message) {
    field.addEventListener('input', () => {
        const val = field.value.trim();
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
validateField(lastnameField, usernameRegex, 'Last name must be letters only, 2-30 chars.');
validateField(emailField, emailRegex, 'Invalid email address.');
validateField(passwordField, passwordRegex, 'Password 6-20 chars, letters & numbers.');
validateField(phoneField, phoneRegex, 'Phone must be 7-20 digits, can include +, spaces, or dashes.');

// Confirm password
retypePasswordField.addEventListener('input', () => {
    if (retypePasswordField.value !== passwordField.value) {
        retypePasswordField.classList.add('is-invalid');
        retypePasswordField.classList.remove('is-valid');
        retypePasswordField.nextElementSibling.textContent = 'Passwords do not match.';
    } else {
        retypePasswordField.classList.remove('is-invalid');
        retypePasswordField.classList.add('is-valid');
        retypePasswordField.nextElementSibling.textContent = '';
    }
});
