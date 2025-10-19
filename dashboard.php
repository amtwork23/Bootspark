<?php
session_start();
require_once "config/database.php";
require_once "classes/User.php";
require_once "classes/Order.php";

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Get user orders
$order = new Order($db);
$orders = $order->getUserOrders($_SESSION['user_id']);
?>
<?php include 'header.php'; ?>

<div class="container">
    <div class="dashboard">
        <div class="welcome-section">
            <h1>Welcome, <?php echo $_SESSION['user_name']; ?>!</h1>
            <p>Manage your account and track your orders</p>
            <div class="quick-actions">
                <a href="products.php" class="action-btn">Continue Shopping</a>
                <a href="cart.php" class="action-btn">View Cart</a>
                <a href="orders.php" class="action-btn">View Orders</a>
                <a href="profile.php" class="action-btn">Profile Picture</a>
            </div>
        </div>
        
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h3>Recent Orders</h3>
                <?php 
                $order_count = 0;
                while($order_row = $orders->fetch(PDO::FETCH_ASSOC)): 
                    $order_count++;
                    if($order_count > 3) break;
                ?>
                <div class="order-item">
                    <p><strong>Order #<?php echo $order_row['id']; ?></strong></p>
                    <p>Amount: ₹<?php echo number_format($order_row['total_amount'], 2); ?></p>
                    <p>Status: <span class="status-<?php echo $order_row['status']; ?>">
                        <?php echo ucfirst($order_row['status']); ?>
                    </span></p>
                    <p>Date: <?php echo date('M j, Y', strtotime($order_row['created_at'])); ?></p>
                </div>
                <?php endwhile; ?>
                
                <?php if($order_count == 0): ?>
                    <p>No orders yet. <a href="products.php">Start shopping!</a></p>
                <?php endif; ?>
            </div>
            
            <div class="dashboard-card">
                <h3>Account Information</h3>
                <p><strong>Name:</strong> <?php echo $_SESSION['user_name']; ?></p>
                <p><strong>Email:</strong> <?php echo $_SESSION['user_email']; ?></p>
                <p><strong>Member since:</strong> <?php echo date('M Y'); ?></p>
                
                <div style="margin-top: 1.5rem;">
                    <a href="logout.php" class="action-btn" style="background: var(--accent);">Logout</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .dashboard {
        padding: 2rem 0;
    }
    
    .welcome-section {
        background: white;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }
    
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
    }
    
    .dashboard-card {
        background: white;
        padding: 1.5rem;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .order-item {
        border-bottom: 1px solid #eee;
        padding: 1rem 0;
    }
    
    .order-item:last-child {
        border-bottom: none;
    }
    
    .status-pending { color: #f39c12; }
    .status-completed { color: #27ae60; }
    .status-cancelled { color: #e74c3c; }
    
    .quick-actions {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
    }
    
    .action-btn {
        padding: 0.5rem 1rem;
        background: var(--secondary);
        color: white;
        text-decoration: none;
        border-radius: 4px;
        transition: background 0.3s;
    }
    
    .action-btn:hover {
        background: #2980b9;
    }
</style>

<?php include 'footer.php'; ?>