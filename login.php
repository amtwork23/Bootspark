<?php
session_start();
require_once "config/database.php";
require_once "classes/User.php";

if($_POST) {
    $database = new Database();
    $db = $database->getConnection();
    
    $user = new User($db);
    
    $user->email = $_POST['email'];
    $email_exists = $user->emailExists();
    
    if($email_exists && $user->verifyPassword($_POST['password'])) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_name'] = $user->name;
        $_SESSION['user_email'] = $user->email;
        
        header("Location: dashboard.php");
        exit;
    } else {
        $login_err = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bootspark</title>
    <style>
        /* Add login-specific styles here */
        .auth-container {
            max-width: 400px;
            margin: 5rem auto;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .auth-form input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            transition: border-color 0.3s;
        }
        
        .auth-form input:focus {
            outline: none;
            border-color: var(--secondary);
        }
        
        .auth-form input.error {
            border-color: var(--accent);
        }
        
        .auth-form input.valid {
            border-color: #27ae60;
        }
        
        .error-message {
            color: var(--accent);
            font-size: 0.8rem;
            margin-top: 0.3rem;
            min-height: 1rem;
        }
        
        .auth-form button {
            width: 100%;
            padding: 0.8rem;
            background: var(--secondary);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .auth-form button:disabled {
            background: #bdc3c7;
            cursor: not-allowed;
        }
        
        .auth-form button:not(:disabled):hover {
            background: #2980b9;
        }
        
        .error {
            color: var(--accent);
            margin-bottom: 1rem;
            text-align: center;
            padding: 0.8rem;
            background: #fee;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <!-- Include header from index.php -->
    <?php include 'header.php'; ?>
    
    <div class="container">
        <div class="auth-container">
            <h2>Login to Your Account</h2>
            <?php if(isset($login_err)): ?>
                <div class="error"><?php echo $login_err; ?></div>
            <?php endif; ?>
            
            <form class="auth-form" method="POST" id="loginForm">
                <div class="form-group">
                    <input type="email" name="email" id="email" placeholder="Email" required>
                    <div class="error-message" id="emailError"></div>
                </div>
                
                <div class="form-group">
                    <input type="password" name="password" id="password" placeholder="Password" required>
                    <div class="error-message" id="passwordError"></div>
                </div>
                
                <button type="submit" id="submitBtn" disabled>Login</button>
            </form>
            
            <p style="text-align: center; margin-top: 1rem;">
                Don't have an account? <a href="register.php">Register here</a>
            </p>
        </div>
    </div>
    
    <!-- Include footer from index.php -->
    <?php include 'footer.php'; ?>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('loginForm');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const submitBtn = document.getElementById('submitBtn');
        
        const emailError = document.getElementById('emailError');
        const passwordError = document.getElementById('passwordError');
        
        // Validation functions
        function validateEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
        
        function validatePassword(password) {
            return password.length >= 1; // Just check if password is not empty for login
        }
        
        function updateFieldValidation(input, isValid, errorElement, errorMessage) {
            if (isValid) {
                input.classList.remove('error');
                input.classList.add('valid');
                errorElement.textContent = '';
            } else {
                input.classList.remove('valid');
                input.classList.add('error');
                errorElement.textContent = errorMessage;
            }
        }
        
        function checkFormValidity() {
            const isEmailValid = validateEmail(emailInput.value);
            const isPasswordValid = validatePassword(passwordInput.value);
            
            const isFormValid = isEmailValid && isPasswordValid;
            submitBtn.disabled = !isFormValid;
        }
        
        // Event listeners
        emailInput.addEventListener('input', function() {
            const isValid = validateEmail(this.value);
            updateFieldValidation(this, isValid, emailError, 'Please enter a valid email address');
            checkFormValidity();
        });
        
        passwordInput.addEventListener('input', function() {
            const isValid = validatePassword(this.value);
            updateFieldValidation(this, isValid, passwordError, 'Password is required');
            checkFormValidity();
        });
        
        // Initial check
        checkFormValidity();
    });
    </script>
</body>
</html>