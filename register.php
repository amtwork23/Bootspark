<?php
session_start();
require_once "config/database.php";
require_once "classes/User.php";

if($_POST) {
    $database = new Database();
    $db = $database->getConnection();
    
    $user = new User($db);
    
    $user->name = $_POST['name'];
    $user->email = $_POST['email'];
    $user->password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Server-side validation
    if(empty($user->name) || empty($user->email) || empty($user->password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif(!filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif(strlen($user->password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif(!preg_match('/[a-z]/', $user->password)) {
        $error = "Password must contain at least one lowercase letter.";
    } elseif(!preg_match('/[A-Z]/', $user->password)) {
        $error = "Password must contain at least one uppercase letter.";
    } elseif(!preg_match('/\d/', $user->password)) {
        $error = "Password must contain at least one number.";
    } elseif(!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $user->password)) {
        $error = "Password must contain at least one symbol.";
    } elseif($user->password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif($user->emailExists()) {
        $error = "Email already registered.";
    } else {
        if($user->register()) {
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_name'] = $user->name;
            $_SESSION['user_email'] = $user->email;
            
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Registration failed. Please try again.";
        }
    }
}
?>
<?php include 'header.php'; ?>

<div class="container">
    <div class="auth-container">
        <h2>Create Account</h2>
        <?php if(isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form class="auth-form" method="POST" id="registerForm">
            <div class="form-group">
                <input type="text" name="name" id="name" placeholder="Full Name" required>
                <div class="error-message" id="nameError"></div>
            </div>
            
            <div class="form-group">
                <input type="email" name="email" id="email" placeholder="Email" required>
                <div class="error-message" id="emailError"></div>
            </div>
            
            <div class="form-group">
                <input type="password" name="password" id="password" placeholder="Password" required>
                <div class="error-message" id="passwordError"></div>
                 <div class="password-requirements">
                     <div id="length" class="requirement">• At least 6 characters (<span id="charCount">0</span>)</div>
                     <div id="lowercase" class="requirement">• One lowercase letter</div>
                     <div id="uppercase" class="requirement">• One uppercase letter</div>
                     <div id="number" class="requirement">• One number</div>
                     <div id="symbol" class="requirement">• One symbol</div>
                 </div>
            </div>
            
            <div class="form-group">
                <input type="password" name="confirm_password" id="confirmPassword" placeholder="Confirm Password" required>
                <div class="error-message" id="confirmPasswordError"></div>
            </div>
            
            <button type="submit" id="submitBtn" disabled>Register</button>
        </form>
        
        <p style="text-align: center; margin-top: 1rem;">
            Already have an account? <a href="login.php">Login here</a>
        </p>
    </div>
</div>

<style>
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
    
    .password-requirements {
        margin-top: 0.5rem;
        font-size: 0.8rem;
    }
    
    .requirement {
        color: #666;
        margin: 0.2rem 0;
        transition: color 0.3s;
    }
    
    .requirement.valid {
        color: #27ae60;
    }
    
    .requirement.invalid {
        color: var(--accent);
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const submitBtn = document.getElementById('submitBtn');
    
    const nameError = document.getElementById('nameError');
    const emailError = document.getElementById('emailError');
    const passwordError = document.getElementById('passwordError');
    const confirmPasswordError = document.getElementById('confirmPasswordError');
    
    // Validation functions
    function validateName(name) {
        return name.trim().length >= 2;
    }
    
    function validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
     function validatePassword(password) {
         const hasLength = password.length >= 6;
         const hasLowercase = /[a-z]/.test(password);
         const hasUppercase = /[A-Z]/.test(password);
         const hasNumber = /\d/.test(password);
         const hasSymbol = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
         
         return { hasLength, hasLowercase, hasUppercase, hasNumber, hasSymbol };
     }
    
     function updatePasswordRequirements(password) {
         const requirements = validatePassword(password);
         
         // Update character counter
         document.getElementById('charCount').textContent = password.length;
         
         document.getElementById('length').className = requirements.hasLength ? 'requirement valid' : 'requirement invalid';
         document.getElementById('lowercase').className = requirements.hasLowercase ? 'requirement valid' : 'requirement invalid';
         document.getElementById('uppercase').className = requirements.hasUppercase ? 'requirement valid' : 'requirement invalid';
         document.getElementById('number').className = requirements.hasNumber ? 'requirement valid' : 'requirement invalid';
         document.getElementById('symbol').className = requirements.hasSymbol ? 'requirement valid' : 'requirement invalid';
         
         return Object.values(requirements).every(req => req);
     }
    
    function validateConfirmPassword(password, confirmPassword) {
        return password === confirmPassword && confirmPassword.length > 0;
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
        const isNameValid = validateName(nameInput.value);
        const isEmailValid = validateEmail(emailInput.value);
        const isPasswordValid = updatePasswordRequirements(passwordInput.value);
        const isConfirmPasswordValid = validateConfirmPassword(passwordInput.value, confirmPasswordInput.value);
        
        const isFormValid = isNameValid && isEmailValid && isPasswordValid && isConfirmPasswordValid;
        submitBtn.disabled = !isFormValid;
    }
    
    // Event listeners
    nameInput.addEventListener('input', function() {
        const isValid = validateName(this.value);
        updateFieldValidation(this, isValid, nameError, 'Name must be at least 2 characters');
        checkFormValidity();
    });
    
    emailInput.addEventListener('input', function() {
        const isValid = validateEmail(this.value);
        updateFieldValidation(this, isValid, emailError, 'Please enter a valid email address');
        checkFormValidity();
    });
    
    passwordInput.addEventListener('input', function() {
        const isValid = updatePasswordRequirements(this.value);
        updateFieldValidation(this, isValid, passwordError, 'Password must meet all requirements');
        checkFormValidity();
    });
    
    confirmPasswordInput.addEventListener('input', function() {
        const isValid = validateConfirmPassword(passwordInput.value, this.value);
        updateFieldValidation(this, isValid, confirmPasswordError, 'Passwords do not match');
        checkFormValidity();
    });
    
    // Initial check
    checkFormValidity();
});
</script>

<?php include 'footer.php'; ?>