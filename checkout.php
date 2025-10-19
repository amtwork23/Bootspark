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

$final_total = $total + 830; // Shipping cost

// Razorpay configuration with YOUR keys
$razorpay_key_id = "rzp_test_5fIpDiq0CC4SjF";
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
                        <input type="text" id="full_name" name="full_name" value="<?php echo $_SESSION['user_name']; ?>" required minlength="2" pattern="[A-Za-z\s]+">
                        <div class="error-message" id="name-error" style="display: none; color: #e74c3c; font-size: 0.9rem; margin-top: 0.5rem;"></div>
                    </div>
                    
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" id="email" name="email" value="<?php echo $_SESSION['user_email']; ?>" required>
                        <div class="error-message" id="email-error" style="display: none; color: #e74c3c; font-size: 0.9rem; margin-top: 0.5rem;"></div>
                    </div>
                    
                    <div class="form-group">
                        <label>Phone Number *</label>
                        <input type="tel" id="phone" name="phone" required placeholder="Enter your 10-digit phone number" maxlength="10" pattern="[0-9]{10}">
                        <div class="error-message" id="phone-error" style="display: none; color: #e74c3c; font-size: 0.9rem; margin-top: 0.5rem;"></div>
                    </div>
                    
                    <div class="form-group">
                        <label>Shipping Address *</label>
                        <textarea id="address" name="address" required placeholder="Enter your complete shipping address with PIN code" minlength="20"></textarea>
                        <div class="error-message" id="address-error" style="display: none; color: #e74c3c; font-size: 0.9rem; margin-top: 0.5rem;"></div>
                        <button type="button" id="verify-address-btn" class="verify-btn" style="margin-top: 0.5rem; padding: 0.5rem 1rem; background: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.9rem;">Verify Address</button>
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
                            <span>₹<?php echo number_format($item_total, 2); ?></span>
                        </div>
                        <?php endwhile; ?>
                        
                        <div class="order-total">
                            <div class="total-row">
                                <span>Subtotal:</span>
                                <span>₹<?php echo number_format($total, 2); ?></span>
                            </div>
                            <div class="total-row">
                                <span>Shipping:</span>
                                <span>₹830.00</span>
                            </div>
                            <div class="total-row grand-total">
                                <span>Total:</span>
                                <span>₹<?php echo number_format($final_total, 2); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" id="rzp-button" class="checkout-btn">Proceed to Payment - ₹<?php echo number_format($final_total, 2); ?></button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Razorpay Checkout Integration -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
const totalAmount = <?php echo $final_total * 100; ?>; // Amount in paise

// Live validation functions
function validateName() {
    const name = document.getElementById('full_name').value.trim();
    const errorDiv = document.getElementById('name-error');
    
    if (name.length < 2) {
        errorDiv.textContent = 'Name must be at least 2 characters long';
        errorDiv.style.display = 'block';
        return false;
    } else if (!/^[A-Za-z\s]+$/.test(name)) {
        errorDiv.textContent = 'Name can only contain letters and spaces';
        errorDiv.style.display = 'block';
        return false;
    } else {
        errorDiv.style.display = 'none';
        return true;
    }
}

function validateEmail() {
    const email = document.getElementById('email').value.trim();
    const errorDiv = document.getElementById('email-error');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (!emailRegex.test(email)) {
        errorDiv.textContent = 'Please enter a valid email address';
        errorDiv.style.display = 'block';
        return false;
    } else {
        errorDiv.style.display = 'none';
        return true;
    }
}

function validatePhone() {
    const phone = document.getElementById('phone').value.trim();
    const errorDiv = document.getElementById('phone-error');
    
    if (phone.length !== 10) {
        errorDiv.textContent = 'Phone number must be exactly 10 digits';
        errorDiv.style.display = 'block';
        return false;
    } else if (!/^[0-9]{10}$/.test(phone)) {
        errorDiv.textContent = 'Phone number can only contain digits';
        errorDiv.style.display = 'block';
        return false;
    } else {
        errorDiv.style.display = 'none';
        return true;
    }
}

function validateAddress() {
    const address = document.getElementById('address').value.trim();
    const errorDiv = document.getElementById('address-error');
    
    if (address.length < 20) {
        errorDiv.textContent = 'Address must be at least 20 characters long';
        errorDiv.style.display = 'block';
        return false;
    } else if (!address.includes(' ')) {
        errorDiv.textContent = 'Please provide a complete address with street, city, and PIN code';
        errorDiv.style.display = 'block';
        return false;
    } else {
        errorDiv.style.display = 'none';
        return true;
    }
}

// Real address validation using geocoding
async function validateRealAddress() {
    const address = document.getElementById('address').value.trim();
    const errorDiv = document.getElementById('address-error');
    const addressInput = document.getElementById('address');
    
    if (address.length < 20) {
        errorDiv.textContent = 'Address must be at least 20 characters long';
        errorDiv.style.display = 'block';
        return false;
    }
    
    // Show loading state
    errorDiv.textContent = 'Verifying address...';
    errorDiv.style.display = 'block';
    errorDiv.style.color = '#3498db';
    addressInput.disabled = true;
    
    try {
        // Try multiple search strategies for better results
        const searchQueries = [
            address, // Full address
            address + ', India', // Add India explicitly
            address.replace(/\d+/g, '').trim() + ', India' // Remove house numbers and add India
        ];
        
        let found = false;
        let bestMatch = null;
        
        for (const query of searchQueries) {
            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=3&countrycodes=in&addressdetails=1`);
                const data = await response.json();
                
                if (data && data.length > 0) {
                    // Check if any result is from India
                    const indianResult = data.find(result => 
                        result.address && (
                            result.address.country === 'India' || 
                            result.address.country_code === 'in' ||
                            result.display_name.includes('India')
                        )
                    );
                    
                    if (indianResult) {
                        found = true;
                        bestMatch = indianResult;
                        break;
                    }
                }
            } catch (e) {
                console.log('Search query failed:', query);
                continue;
            }
        }
        
        if (found) {
            // Address found and verified
            errorDiv.textContent = '✓ Address verified';
            errorDiv.style.color = '#27ae60';
            errorDiv.style.display = 'block';
            addressInput.disabled = false;
            return true;
        } else {
            // Try a more lenient approach - check if it contains Indian city/state names
            const indianCities = ['Mumbai', 'Delhi', 'Bangalore', 'Chennai', 'Kolkata', 'Hyderabad', 'Pune', 'Ahmedabad', 'Jaipur', 'Surat', 'Lucknow', 'Kanpur', 'Nagpur', 'Indore', 'Thane', 'Bhopal', 'Visakhapatnam', 'Pimpri', 'Patna', 'Vadodara'];
            const indianStates = ['Maharashtra', 'Delhi', 'Karnataka', 'Tamil Nadu', 'West Bengal', 'Telangana', 'Gujarat', 'Rajasthan', 'Uttar Pradesh', 'Madhya Pradesh', 'Andhra Pradesh', 'Bihar', 'Odisha', 'Kerala', 'Assam', 'Punjab', 'Haryana'];
            
            const addressLower = address.toLowerCase();
            const hasIndianCity = indianCities.some(city => addressLower.includes(city.toLowerCase()));
            const hasIndianState = indianStates.some(state => addressLower.includes(state.toLowerCase()));
            const hasPinCode = /\d{6}/.test(address);
            
            if (hasIndianCity || hasIndianState || hasPinCode) {
                // Likely a valid Indian address based on keywords
                errorDiv.textContent = '✓ Address appears valid (contains Indian location)';
                errorDiv.style.color = '#27ae60';
                errorDiv.style.display = 'block';
                addressInput.disabled = false;
                return true;
            } else {
                // Address not found
                errorDiv.textContent = 'Address not found. Please check and enter a valid Indian address with city/state name';
                errorDiv.style.color = '#e74c3c';
                errorDiv.style.display = 'block';
                addressInput.disabled = false;
                return false;
            }
        }
    } catch (error) {
        console.error('Address validation error:', error);
        // Fallback to basic validation if API fails
        errorDiv.textContent = 'Unable to verify address. Please ensure it\'s a valid Indian address';
        errorDiv.style.color = '#f39c12';
        errorDiv.style.display = 'block';
        addressInput.disabled = false;
        return false;
    }
}

// Add event listeners for live validation
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('full_name').addEventListener('blur', validateName);
    document.getElementById('full_name').addEventListener('input', function() {
        if (document.getElementById('name-error').style.display === 'block') {
            validateName();
        }
    });
    
    document.getElementById('email').addEventListener('blur', validateEmail);
    document.getElementById('email').addEventListener('input', function() {
        if (document.getElementById('email-error').style.display === 'block') {
            validateEmail();
        }
    });
    
    document.getElementById('phone').addEventListener('blur', validatePhone);
    document.getElementById('phone').addEventListener('input', function() {
        // Only allow digits
        this.value = this.value.replace(/[^0-9]/g, '');
        if (document.getElementById('phone-error').style.display === 'block') {
            validatePhone();
        }
    });
    
    document.getElementById('address').addEventListener('blur', validateAddress);
    document.getElementById('address').addEventListener('input', function() {
        if (document.getElementById('address-error').style.display === 'block') {
            validateAddress();
        }
        
        // Reset verify button if address is changed
        const verifyBtn = document.getElementById('verify-address-btn');
        if (verifyBtn.disabled) {
            verifyBtn.textContent = 'Verify Address';
            verifyBtn.style.background = '#3498db';
            verifyBtn.disabled = false;
        }
    });
    
    // Add verify address button functionality
    document.getElementById('verify-address-btn').addEventListener('click', async function() {
        const isValid = await validateRealAddress();
        if (isValid) {
            this.textContent = '✓ Verified';
            this.style.background = '#27ae60';
            this.disabled = true;
        } else {
            this.textContent = 'Verify Address';
            this.style.background = '#3498db';
        }
    });
});

document.getElementById('rzp-button').onclick = async function(e) {
    // Validate all fields using custom validation
    const isNameValid = validateName();
    const isEmailValid = validateEmail();
    const isPhoneValid = validatePhone();
    const isAddressValid = validateAddress();
    
    // Check if address has been verified
    const verifyBtn = document.getElementById('verify-address-btn');
    const isAddressVerified = verifyBtn.disabled && verifyBtn.textContent.includes('✓');
    
    if (!isNameValid || !isEmailValid || !isPhoneValid || !isAddressValid) {
        // Scroll to first error
        const firstError = document.querySelector('.error-message[style*="block"]');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return;
    }
    
    if (!isAddressVerified) {
        alert('Please verify your address before proceeding to payment.');
        verifyBtn.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }

    // Show loading state
    this.disabled = true;
    this.innerHTML = 'Creating Order...';
    
    try {
        // Create Razorpay order first
        const orderResponse = await fetch('create_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                amount: totalAmount,
                currency: 'INR'
            })
        });
        
        const orderData = await orderResponse.json();
        
        if (orderData.error) {
            throw new Error(orderData.error);
    }

    const options = {
        "key": "rzp_test_5fIpDiq0CC4SjF",
        "amount": totalAmount,
        "currency": "INR",
            "order_id": orderData.order_id,
        "name": "Bootspark Shoes",
        "description": "Shoe Purchase Order",
            "image": "https://bootspark.com/logo.png",
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
        },
        "modal": {
            "ondismiss": function(){
                // Handle when user closes the payment form
                console.log("Payment form closed");
                    // Reset button state
                    const btn = document.getElementById('rzp-button');
                    btn.disabled = false;
                    btn.innerHTML = 'Proceed to Payment - ₹<?php echo number_format($final_total, 2); ?>';
            }
        }
    };
    
    var rzp = new Razorpay(options);
    rzp.open();
        
    } catch (error) {
        console.error('Error creating order:', error);
        alert('Failed to create order. Please try again.');
        this.disabled = false;
        this.innerHTML = 'Proceed to Payment - ₹<?php echo number_format($final_total, 2); ?>';
    }
    
    e.preventDefault();
}

function processPaymentSuccess(response) {
    // Show loading state
    document.getElementById('rzp-button').disabled = true;
    document.getElementById('rzp-button').innerHTML = 'Processing...';
    
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
        font-weight: bold;
    }
    
    .checkout-btn:hover {
        background: #c0392b;
    }
    
    .checkout-btn:disabled {
        background: #95a5a6;
        cursor: not-allowed;
    }
    
    /* Validation styles */
    .form-group input:invalid, .form-group textarea:invalid {
        border-color: #e74c3c;
    }
    
    .form-group input:valid, .form-group textarea:valid {
        border-color: #27ae60;
    }
    
    .error-message {
        transition: all 0.3s ease;
    }
    
    .form-group input:focus, .form-group textarea:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
    }
    
    .verify-btn {
        transition: all 0.3s ease;
    }
    
    .verify-btn:hover:not(:disabled) {
        background: #2980b9 !important;
        transform: translateY(-1px);
    }
    
    .verify-btn:disabled {
        cursor: not-allowed;
        opacity: 0.8;
    }
</style>

<?php include 'footer.php'; ?>