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
<?php include 'header.php'; ?>

<div class="container">
    <div class="auth-container">
        <h2>Login to Your Account</h2>
        <?php if(isset($login_err)): ?>
            <div class="error"><?php echo $login_err; ?></div>
        <?php endif; ?>
        
        <form class="auth-form" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        
        <p style="text-align: center; margin-top: 1rem;">
            Don't have an account? <a href="register.php">Register here</a>
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