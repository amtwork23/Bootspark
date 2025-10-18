<?php
session_start();
require_once "config/database.php";
require_once "classes/Cart.php";
require_once "classes/Order.php";

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

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

// Razorpay configuration
$razorpay_key_id = "rzp_test_YOUR_KEY_ID"; // Replace with your actual Key ID
?>
<?php include 'header.php'; ?>

<div class="container">
    <div class="checkout-page">
        <h1>Checkout</h1>
        
        <div class="checkout-content">
            <div class="checkout-form">
                <h2>Shipping Information</h2>
                <form id="order-form">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" id="full_name" name="full_name" value="<?php echo $_SESSION['user_name']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" id="email" name="email" value="<?php echo $_SESSION['user_email']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Phone Number *</label>
                        <input type="tel" id="phone" name="phone" required placeholder="+91 9876543210">
                    </div>
                    
                    <div class="form-group">
                        <label>Shipping Address *</label>
                        <textarea id="address" name="address" required placeholder="Enter your complete shipping address with PIN code">123 Main Street, City, State, 560001</textarea>
                    </div>
                    
                    <h2>Order Summary</h2>
                    <div class="order-summary">
                        <?php 
                        $cart_items = $cart->getCartItems($_SESSION['user_id']);
                        $total = 0;
                        while($item = $cart_items->fetch(PDO::FETCH_ASSOC)): 
                            $item_total = $item['price'] * $item['quantity'];
                            $total += $item_total;
                        ?>
                        <div class="order-item">
                            <span><?php echo $item['name']; ?> x <?php echo $item['quantity']; ?></span>
                            <span>₹<?php echo number_format($item_total * 83, 2); ?></span>
                        </div>
                        <?php endwhile; ?>
                        
                        <div class="order-total">
                            <div class="total-row">
                                <span>Subtotal:</span>
                                <span>₹<?php echo number_format($total * 83, 2); ?></span>
                            </div>
                            <div class="total-row">
                                <span>Shipping:</span>
                                <span>₹<?php echo number_format(10 * 83, 2); ?></span>
                            </div>
                            <div class="total-row grand-total">
                                <span>Total:</span>
                                <span>₹<?php echo number_format($final_total * 83, 2); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" id="rzp-button" class="checkout-btn">Proceed to Payment</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Razorpay Checkout Integration -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
// Convert dollars to rupees (approximate conversion)
const exchangeRate = 83;
const totalAmount = <?php echo $final_total; ?> * exchangeRate * 100; // Amount in paise

document.getElementById('rzp-button').onclick = function(e) {
    // Validate form
    const form = document.getElementById('order-form');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const options = {
        "key": "<?php echo $razorpay_key_id; ?>", // Enter your Razorpay Key ID here
        "amount": totalAmount, // Amount in paise
        "currency": "INR",
        "name": "StrideRight Shoes",
        "description": "Shoe Purchase",
        "image": "https://example.com/your_logo.jpg", // Add your logo URL
        "handler": function (response){
            // On successful payment
            processPaymentSuccess(response);
        },
        "prefill": {
            "name": document.getElementById('full_name').value,
            "email": document.getElementById('email').value,
            "contact": document.getElementById('phone').value
        },
        "notes": {
            "address": document.getElementById('address').value,
            "user_id": "<?php echo $_SESSION['user_id']; ?>"
        },
        "theme": {
            "color": "#3498db"
        }
    };
    
    var rzp = new Razorpay(options);
    rzp.open();
    e.preventDefault();
}

function processPaymentSuccess(response) {
    // Create form to submit payment data
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'payment-process.php';
    
    // Add payment details
    const addInput = (name, value) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.appendChild(input);
    };
    
    addInput('razorpay_payment_id', response.razorpay_payment_id);
    addInput('razorpay_order_id', response.razorpay_order_id);
    addInput('razorpay_signature', response.razorpay_signature);
    addInput('amount', totalAmount);
    addInput('full_name', document.getElementById('full_name').value);
    addInput('email', document.getElementById('email').value);
    addInput('phone', document.getElementById('phone').value);
    addInput('address', document.getElementById('address').value);
    
    document.body.appendChild(form);
    form.submit();
}
</script>

<style>
    .checkout-page {
        padding: 2rem 0;
    }
    
    .checkout-content {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
    }
    
    .checkout-form {
        background: white;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }
    
    .form-group input, .form-group textarea {
        width: 100%;
        padding: 0.8rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1rem;
    }
    
    .form-group textarea {
        height: 100px;
        resize: vertical;
    }
    
    .order-summary {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 8px;
        margin: 1.5rem 0;
    }
    
    .order-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #ddd;
    }
    
    .order-total {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 2px solid #ddd;
    }
    
    .total-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }
    
    .grand-total {
        font-size: 1.2rem;
        font-weight: bold;
        color: var(--accent);
    }
    
    .checkout-btn {
        width: 100%;
        padding: 1rem;
        background: var(--accent);
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 1.1rem;
        cursor: pointer;
        transition: background 0.3s;
    }
    
    .checkout-btn:hover {
        background: #c0392b;
    }
    
    .error-message {
        background: #f8d7da;
        color: #721c24;
        padding: 1rem;
        border-radius: 4px;
        margin-bottom: 1rem;
    }
</style>

<?php include 'footer.php'; ?>