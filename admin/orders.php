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

// Handle status update
if(isset($_POST['update_status'])) {
    $order->order_id = $_POST['order_id'];
    if($order->updateStatus($_POST['status'])) {
        $message = "Order status updated successfully!";
    } else {
        $error = "Failed to update order status.";
    }
}

$orders = $order->getAllOrders();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - StrideRight Admin</title>
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        
        <div class="main-content">
            <div class="admin-header">
                <h1>Manage Orders</h1>
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
                
                <div class="card">
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Payment ID</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $orders->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td>#<?php echo $row['id']; ?></td>
                                    <td>User #<?php echo $row['user_id']; ?></td>
                                    <td>$<?php echo $row['total_amount']; ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                            <select name="status" onchange="this.form.submit()" style="padding: 0.3rem; border: 1px solid #ddd; border-radius: 4px;">
                                                <option value="pending" <?php echo $row['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="confirmed" <?php echo $row['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                                <option value="shipped" <?php echo $row['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                <option value="delivered" <?php echo $row['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                <option value="cancelled" <?php echo $row['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                            <input type="hidden" name="update_status" value="1">
                                        </form>
                                    </td>
                                    <td><?php echo $row['payment_id'] ? substr($row['payment_id'], 0, 10) . '...' : 'N/A'; ?></td>
                                    <td><?php echo date('M j, Y', strtotime($row['created_at'])); ?></td>
                                    <td>
                                        <a href="order_details.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>