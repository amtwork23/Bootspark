<?php
require_once "functions.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bootspark - Men's Shoes</title>
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f9f9f9;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        /* Header Styles */
        header {
            background-color: var(--primary);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.8rem;
            font-weight: bold;
        }
        
        .logo a {
            color: white;
            text-decoration: none;
        }
        
        .logo span {
            color: var(--secondary);
        }
        
        nav ul {
            display: flex;
            list-style: none;
        }
        
        nav ul li {
            margin-left: 1.5rem;
        }
        
        nav ul li a {
            color: white;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        nav ul li a:hover {
            color: var(--secondary);
        }
        
        .auth-buttons button {
            background: var(--secondary);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 0.5rem;
            transition: background 0.3s;
        }
        
        .auth-buttons button:hover {
            background: #2980b9;
        }
        
        /* User Profile Styles */
        .user-profile {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            position: relative;
        }
        
        .profile-picture {
            position: relative;
        }
        
        .profile-img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--secondary);
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .profile-img:hover {
            border-color: white;
            transform: scale(1.1);
        }
        
        .profile-placeholder {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--secondary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .profile-placeholder:hover {
            background: white;
            color: var(--secondary);
            transform: scale(1.1);
        }
        
        .user-menu {
            position: relative;
        }
        
        .user-name {
            color: white;
            font-weight: 500;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 4px;
            transition: background 0.3s;
        }
        
        .user-name:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            min-width: 180px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s;
            z-index: 1000;
        }
        
        .user-profile:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .dropdown-menu a {
            display: block;
            padding: 0.8rem 1rem;
            color: #333;
            text-decoration: none;
            transition: background 0.3s;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .dropdown-menu a:last-child {
            border-bottom: none;
        }
        
        .dropdown-menu a:hover {
            background: #f8f9fa;
            color: var(--secondary);
        }
        
        .dropdown-menu a:first-child {
            border-radius: 8px 8px 0 0;
        }
        
        .dropdown-menu a:last-child {
            border-radius: 0 0 8px 8px;
        }
        
        /* Footer Styles */
        footer {
            background: var(--dark);
            color: white;
            padding: 3rem 0 2rem;
            margin-top: 3rem;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
        }
        
        .footer-section h3 {
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
        }
        
        .footer-section ul {
            list-style: none;
        }
        
        .footer-section ul li {
            margin-bottom: 0.5rem;
        }
        
        .footer-section a {
            color: #bbb;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-section a:hover {
            color: white;
        }
        
        .footer-bottom {
            text-align: center;
            margin-top: 3rem;
            padding-top: 1.5rem;
            border-top: 1px solid #444;
            color: #bbb;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
            }
            
            nav ul {
                margin-top: 1rem;
                justify-content: center;
            }
            
            .auth-buttons {
                margin-top: 1rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="index.php"><span>Boot</span><span>spark</span></a>
                </div>
                <nav>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="products.php">Products</a></li>
                        <li><a href="about.php">About</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </nav>
                <div class="auth-buttons">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <div class="user-profile">
                            <?php
                            // Get user profile picture
                            require_once "config/database.php";
                            require_once "classes/User.php";
                            $database = new Database();
                            $db = $database->getConnection();
                            $user = new User($db);
                            $profile_picture = $user->getProfilePicture($_SESSION['user_id']);
                            ?>
                            <div class="profile-picture">
                                <?php if($profile_picture && file_exists($profile_picture)): ?>
                                    <img src="<?php echo $profile_picture; ?>" alt="Profile" class="profile-img">
                                <?php else: ?>
                                    <div class="profile-placeholder"><?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="user-menu">
                                <span class="user-name"><?php echo $_SESSION['user_name']; ?></span>
                                <div class="dropdown-menu">
                                    <a href="dashboard.php">Dashboard</a>
                                    <a href="profile.php">Profile</a>
                                    <a href="orders.php">Orders</a>
                                    <a href="cart.php">Cart</a>
                                    <a href="logout.php">Logout</a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="login.php"><button>Login</button></a>
                        <a href="register.php"><button>Register</button></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>