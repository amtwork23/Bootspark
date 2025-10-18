<?php
session_start();
require_once "../config/database.php";
require_once "../classes/Order.php";

if(!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$order = new Order($db);

// Get order ID from URL
$order_id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Order ID not found.');

// Get order details
$order_details = $order->getOrderDetails($order_id);
$order_items = $order->getOrderItems($order_id);

// Handle status update
if(isset($_POST['update_status'])) {
    $order->order_id = $order_id;
    if($order->updateStatus($_POST['status'])) {
        $message = "Order status updated successfully!";
        // Refresh order details
        $order_details = $order->getOrderDetails($order_id);
    } else {
        $error = "Failed to update order status.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - StrideRight Admin</title>
    <style>
        .order-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .detail-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .detail-group {
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }
        
        .detail-group:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .detail-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 0.3rem;
        }
        
        .detail-value {
            color: #333;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .items-table th, .items-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .items-table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        
        .item-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        .status-form {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        
        .total-row {
            font-weight: bold;
            font-size: 1.1rem;
            background: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        
        <div class="main-content">
            <div class="admin-header">
                <h1>Order Details - #<?php echo $order_id; ?></h1>
                <a href="orders.php" class="btn btn-secondary">← Back to Orders</a>
            </div>
            
            <div class="admin-body">
                <?php if(isset($message)): ?>
                    <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if(isset($error)): ?>
                    <div style="background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <div class="order-details">
                    <!-- Order Information -->
                    <div class="detail-card">
                        <h3>Order Information</h3>
                        <div class="detail-group">
                            <div class="detail-label">Order ID</div>
                            <div class="detail-value">#<?php echo $order_details['id']; ?></div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Order Date</div>
                            <div class="detail-value"><?php echo date('F j, Y g:i A', strtotime($order_details['created_at'])); ?></div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Total Amount</div>
                            <div class="detail-value" style="font-size: 1.2rem; font-weight: bold; color: #e74c3c;">
                                $<?php echo $order_details['total_amount']; ?>
                            </div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Payment ID</div>
                            <div class="detail-value"><?php echo $order_details['payment_id'] ?: 'N/A'; ?></div>
                        </div>
                        <div class="detail-group">
                            <form method="POST" class="status-form">
                                <div class="detail-label">Order Status</div>
                                <select name="status" style="padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
                                    <option value="pending" <?php echo $order_details['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="confirmed" <?php echo $order_details['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                    <option value="shipped" <?php echo $order_details['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="delivered" <?php echo $order_details['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="cancelled" <?php echo $order_details['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-sm btn-primary">Update</button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Customer Information -->
                    <div class="detail-card">
                        <h3>Customer Information</h3>
                        <div class="detail-group">
                            <div class="detail-label">Customer Name</div>
                            <div class="detail-value"><?php echo $order_details['user_name'] ?: 'User #' . $order_details['user_id']; ?></div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Email</div>
                            <div class="detail-value"><?php echo $order_details['email'] ?: 'N/A'; ?></div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">User ID</div>
                            <div class="detail-value"><?php echo $order_details['user_id']; ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Shipping Address -->
                <div class="detail-card" style="grid-column: 1 / -1;">
                    <h3>Shipping Address</h3>
                    <div class="detail-value" style="white-space: pre-line;"><?php echo $order_details['address']; ?></div>
                </div>
                
                <!-- Order Items -->
                <div class="detail-card" style="grid-column: 1 / -1;">
                    <h3>Order Items</h3>
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $subtotal = 0;
                            while($item = $order_items->fetch(PDO::FETCH_ASSOC)): 
                                $item_total = $item['price'] * $item['quantity'];
                                $subtotal += $item_total;
                            ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        <img src="../assets/images/<?php echo $item['image']; ?>" 
                                             alt="<?php echo $item['name']; ?>" 
                                             class="item-image"
                                             onerror="this.src='https://via.placeholder.com/50x50?text=No+Image'">
                                        <div><?php echo $item['name']; ?></div>
                                    </div>
                                </td>
                                <td>$<?php echo $item['price']; ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>$<?php echo number_format($item_total, 2); ?></td>
                            </tr>
                            <?php endwhile; ?>
                            <tr class="total-row">
                                <td colspan="3" style="text-align: right;">Subtotal:</td>
                                <td>$<?php echo number_format($subtotal, 2); ?></td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="3" style="text-align: right;">Shipping:</td>
                                <td>$10.00</td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="3" style="text-align: right;">Grand Total:</td>
                                <td>$<?php echo number_format($order_details['total_amount'], 2); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>