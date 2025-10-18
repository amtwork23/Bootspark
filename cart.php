<?php
session_start();
require_once "config/database.php";
require_once "classes/Cart.php";
require_once "classes/Product.php";
require_once "functions.php";

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$cart = new Cart($db);

// Handle add to cart
if($_POST && isset($_POST['add_to_cart'])) {
    $cart->user_id = $_SESSION['user_id'];
    $cart->product_id = $_POST['product_id'];
    $cart->quantity = $_POST['quantity'];
    
    if($cart->addToCart()) {
        $message = "Product added to cart successfully!";
    } else {
        $error = "Failed to add product to cart.";
    }
}

// Handle remove from cart
if(isset($_GET['remove'])) {
    $cart->user_id = $_SESSION['user_id'];
    $cart->product_id = $_GET['remove'];
    
    if($cart->removeFromCart()) {
        $message = "Product removed from cart.";
    } else {
        $error = "Failed to remove product from cart.";
    }
}

// Handle update quantity
if($_POST && isset($_POST['update_quantity'])) {
    $cart->user_id = $_SESSION['user_id'];
    $cart->product_id = $_POST['product_id'];
    $cart->quantity = $_POST['quantity'];
    
    if($cart->updateQuantity()) {
        $message = "Cart updated successfully!";
    } else {
        $error = "Failed to update cart.";
    }
}

// Get cart items
$cart_items = $cart->getCartItems($_SESSION['user_id']);
$total = 0;
?>
<?php include 'header.php'; ?>

<div class="container">
    <div class="cart-page">
        <h1>Shopping Cart</h1>
        
        <?php if(isset($message)): ?>
            <div class="success-message"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($cart_items->rowCount() > 0): ?>
        <div class="cart-items">
            <?php while($item = $cart_items->fetch(PDO::FETCH_ASSOC)): 
                $item_total = $item['price'] * $item['quantity'];
                $total += $item_total;
            ?>
            <div class="cart-item">
                <div class="item-image">
                    <!-- UPDATED IMAGE CODE -->
                    <img src="<?php echo getProductImage($item['name']); ?>" alt="<?php echo $item['name']; ?>">
                </div>
                
                <div class="item-details">
                    <h3 class="item-name"><?php echo $item['name']; ?></h3>
                    <p class="item-price">₹<?php echo number_format($item['price'], 2); ?></p>
                    
                    <div class="quantity-controls">
                        <!-- ... quantity controls ... -->
                    </div>
                </div>
                
                <div style="text-align: right;">
                    <p style="font-size: 1.2rem; font-weight: bold; margin-bottom: 1rem;">
                        ₹<?php echo number_format($item_total, 2); ?>
                    </p>
                    <a href="cart.php?remove=<?php echo $item['product_id']; ?>" class="remove-btn">Remove</a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        
        <div class="cart-summary">
            <div class="summary-row">
                <span>Subtotal:</span>
                <span>₹<?php echo number_format($total, 2); ?></span>
            </div>
            <div class="summary-row">
                <span>Shipping:</span>
                <span>₹<?php echo number_format(830, 2); ?></span> <!-- 10 * 83 -->
            </div>
            <div class="summary-row summary-total">
                <span>Total:</span>
                <span>₹<?php echo number_format($total + 830, 2); ?></span>
            </div>
            
            <div style="text-align: center; margin-top: 2rem;">
                <a href="products.php" class="action-btn" style="background: #666; margin-right: 1rem;">Continue Shopping</a>
                <a href="checkout.php" class="action-btn">Proceed to Checkout</a>
            </div>
        </div>
        
        <?php else: ?>
        <div class="empty-cart">
            <h2>Your cart is empty</h2>
            <p>Browse our collection and add some stylish shoes to your cart!</p>
            <a href="products.php" class="cta-button">Start Shopping</a>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .cart-page {
        padding: 2rem 0;
    }
    
    .cart-items {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    
    .cart-item {
        display: flex;
        align-items: center;
        padding: 1.5rem;
        border-bottom: 1px solid #eee;
    }
    
    .cart-item:last-child {
        border-bottom: none;
    }
    
    .item-image {
        width: 100px;
        height: 100px;
        margin-right: 1.5rem;
    }
    
    .item-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 4px;
    }
    
    .item-details {
        flex: 1;
    }
    
    .item-name {
        font-size: 1.2rem;
        margin-bottom: 0.5rem;
    }
    
    .item-price {
        font-size: 1.1rem;
        color: var(--accent);
        font-weight: bold;
    }
    
    .quantity-controls {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin: 1rem 0;
    }
    
    .quantity-btn {
        background: #f8f9fa;
        border: 1px solid #ddd;
        width: 30px;
        height: 30px;
        border-radius: 4px;
        cursor: pointer;
    }
    
    .quantity-input {
        width: 50px;
        text-align: center;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 0.3rem;
    }
    
    .remove-btn {
        background: var(--accent);
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }
    
    .cart-summary {
        background: white;
        padding: 1.5rem;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-top: 2rem;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #eee;
    }
    
    .summary-total {
        font-size: 1.3rem;
        font-weight: bold;
        color: var(--accent);
    }
    
    .empty-cart {
        text-align: center;
        padding: 3rem;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .success-message {
        background: #d4edda;
        color: #155724;
        padding: 1rem;
        border-radius: 4px;
        margin-bottom: 1rem;
    }
    
    .error-message {
        background: #f8d7da;
        color: #721c24;
        padding: 1rem;
        border-radius: 4px;
        margin-bottom: 1rem;
    }
    
    .action-btn {
        padding: 0.8rem 1.5rem;
        background: var(--secondary);
        color: white;
        text-decoration: none;
        border-radius: 4px;
        display: inline-block;
    }
</style>

<?php include 'footer.php'; ?>