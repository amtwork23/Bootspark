<?php
session_start();
require_once "config/database.php";
require_once "classes/Order.php";

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();

$order = new Order($db);
$orders = $order->getUserOrders($_SESSION['user_id']);
?>
<?php include 'header.php'; ?>

<div class="container">
    <div class="orders-page">
        <h1>My Orders</h1>
        
        <?php if($orders->rowCount() > 0): ?>
            <?php while($order_row = $orders->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <h3>Order #<?php echo $order_row['id']; ?></h3>
                        <p>Placed on <?php echo date('F j, Y', strtotime($order_row['created_at'])); ?></p>
                    </div>
                    <div>
                        <span class="status-badge status-<?php echo $order_row['status']; ?>">
                            <?php echo ucfirst($order_row['status']); ?>
                        </span>
                    </div>
                </div>
                
                <div class="order-details">
                    <p><strong>Total Amount:</strong> $<?php echo $order_row['total_amount']; ?></p>
                    <p><strong>Shipping Address:</strong> <?php echo $order_row['address']; ?></p>
                    
                    <?php if($order_row['payment_id']): ?>
                        <p><strong>Payment ID:</strong> <?php echo $order_row['payment_id']; ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-orders">
                <h2>No orders yet</h2>
                <p>You haven't placed any orders with us yet.</p>
                <a href="products.php" class="cta-button">Start Shopping</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .orders-page {
        padding: 2rem 0;
    }
    
    .order-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
        overflow: hidden;
    }
    
    .order-header {
        background: #f8f9fa;
        padding: 1.5rem;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .order-details {
        padding: 1.5rem;
    }
    
    .status-badge {
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: bold;
    }
    
    .status-pending { background: #fff3cd; color: #856404; }
    .status-completed { background: #d1edff; color: #004085; }
    .status-shipped { background: #d4edda; color: #155724; }
    .status-cancelled { background: #f8d7da; color: #721c24; }
    
    .no-orders {
        text-align: center;
        padding: 3rem;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
</style>

<?php include 'footer.php'; ?>