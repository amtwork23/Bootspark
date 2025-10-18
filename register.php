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
    
    if($user->emailExists()) {
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
        
        <form class="auth-form" method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required minlength="6">
            <button type="submit">Register</button>
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
    
    .auth-form input {
        width: 100%;
        padding: 0.8rem;
        margin-bottom: 1rem;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    
    .auth-form button {
        width: 100%;
        padding: 0.8rem;
        background: var(--secondary);
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
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

<?php include 'footer.php'; ?>