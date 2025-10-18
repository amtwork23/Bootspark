<?php
session_start();
require_once "config/database.php";
require_once "classes/Order.php";
require_once "classes/Cart.php";

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Razorpay configuration
$razorpay_key_id = "rzp_test_YOUR_KEY_ID"; // Replace with your Key ID
$razorpay_key_secret = "YOUR_KEY_SECRET"; // Replace with your Key Secret

if($_POST) {
    $database = new Database();
    $db = $database->getConnection();
    
    $cart = new Cart($db);
    $cart_items = $cart->getCartItems($_SESSION['user_id']);
    
    // Calculate total
    $total = 0;
    while($item = $cart_items->fetch(PDO::FETCH_ASSOC)) {
        $total += $item['price'] * $item['quantity'];
    }
    $final_total = $total + 10; // Shipping cost
    
    // Verify payment signature
    $generated_signature = hash_hmac('sha256', $_POST['razorpay_order_id'] . '|' . $_POST['razorpay_payment_id'], $razorpay_key_secret);
    
    if ($generated_signature == $_POST['razorpay_signature']) {
        // Payment is successful and verified
        
        // Create order
        $order = new Order($db);
        $order->user_id = $_SESSION['user_id'];
        $order->total_amount = $final_total;
        $order->address = $_POST['address'];
        
        if($order->createOrder()) {
            // Update payment status
            $order->updatePaymentStatus($_POST['razorpay_payment_id'], 'completed');
            
            $message = "Payment successful! Your order has been placed.";
            $order_id = $order->order_id;
            $payment_id = $_POST['razorpay_payment_id'];
            $status = 'success';
        } else {
            $message = "Order creation failed. Please contact support.";
            $status = 'error';
        }
    } else {
        $message = "Payment verification failed. Please contact support.";
        $status = 'error';
    }
} else {
    header("Location: checkout.php");
    exit;
}
?>
<?php include 'header.php'; ?>

<div class="container">
    <div class="payment-status">
        <?php if($status == 'success'): ?>
        <div class="status-card success">
            <div class="status-icon">
                ✓
            </div>
            <h2>Payment Successful!</h2>
            <p><?php echo $message; ?></p>
            
            <div class="order-details">
                <p><strong>Order ID:</strong> #<?php echo $order_id; ?></p>
                <p><strong>Payment ID:</strong> <?php echo $payment_id; ?></p>
                <p><strong>Amount Paid:</strong> ₹<?php echo number_format($final_total * 83, 2); ?></p>
                <p><strong>Status:</strong> <span class="status-completed">Completed</span></p>
            </div>
            
            <div class="action-buttons">
                <a href="orders.php" class="btn btn-primary">View Your Orders</a>
                <a href="products.php" class="btn btn-secondary">Continue Shopping</a>
            </div>
        </div>
        <?php else: ?>
        <div class="status-card error">
            <div class="status-icon">
                ✗
            </div>
            <h2>Payment Failed</h2>
            <p><?php echo $message; ?></p>
            
            <div class="action-buttons">
                <a href="checkout.php" class="btn btn-primary">Try Again</a>
                <a href="cart.php" class="btn btn-secondary">Back to Cart</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .payment-status {
        padding: 4rem 0;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 60vh;
    }
    
    .status-card {
        background: white;
        padding: 3rem;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        text-align: center;
        max-width: 500px;
        width: 100%;
    }
    
    .status-card.success {
        border-top: 4px solid #27ae60;
    }
    
    .status-card.error {
        border-top: 4px solid #e74c3c;
    }
    
    .status-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        margin: 0 auto 2rem;
    }
    
    .status-card.success .status-icon {
        background: #d4edda;
        color: #27ae60;
    }
    
    .status-card.error .status-icon {
        background: #f8d7da;
        color: #e74c3c;
    }
    
    .status-card h2 {
        margin-bottom: 1rem;
    }
    
    .status-card.success h2 {
        color: #27ae60;
    }
    
    .status-card.error h2 {
        color: #e74c3c;
    }
    
    .order-details {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 8px;
        margin: 2rem 0;
        text-align: left;
    }
    
    .order-details p {
        margin-bottom: 0.5rem;
    }
    
    .status-completed {
        color: #27ae60;
        font-weight: bold;
    }
    
    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 2rem;
    }
    
    .btn {
        padding: 0.8rem 1.5rem;
        border-radius: 4px;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s;
    }
    
    .btn-primary {
        background: var(--secondary);
        color: white;
    }
    
    .btn-primary:hover {
        background: #2980b9;
    }
    
    .btn-secondary {
        background: #6c757d;
        color: white;
    }
    
    .btn-secondary:hover {
        background: #545b62;
    }
</style>

<?php include 'footer.php'; ?>