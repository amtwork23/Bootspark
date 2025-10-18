<?php
session_start();
require_once "../config/database.php";
require_once "../classes/User.php";
require_once "../classes/Order.php";

if(!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Get user ID from URL
$user_id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: User ID not found.');

// Get user details
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bindParam(1, $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$user) {
    die('ERROR: User not found.');
}

// Get user orders
$order = new Order($db);
$user_orders = $order->getUserOrders($user_id);

// Get order statistics
$stmt = $db->prepare("SELECT COUNT(*) as order_count, SUM(total_amount) as total_spent FROM orders WHERE user_id = ?");
$stmt->bindParam(1, $user_id);
$stmt->execute();
$order_stats = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details - StrideRight Admin</title>
    <style>
        .user-details {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 2rem;
        }
        
        .detail-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .user-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .stat-card {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 6px;
            text-align: center;
        }
        
        .stat-number {
            font-size: 1.5rem;
            font-weight: bold;
            color: #3498db;
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: #666;
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
        
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .orders-table th, .orders-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .orders-table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        
        .no-orders {
            text-align: center;
            padding: 2rem;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        
        <div class="main-content">
            <div class="admin-header">
                <h1>User Details</h1>
                <a href="users.php" class="btn btn-secondary">← Back to Users</a>
            </div>
            
            <div class="admin-body">
                <div class="user-details">
                    <!-- User Information -->
                    <div class="detail-card">
                        <h3>User Information</h3>
                        <div class="detail-group">
                            <div class="detail-label">User ID</div>
                            <div class="detail-value">#<?php echo $user['id']; ?></div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Full Name</div>
                            <div class="detail-value"><?php echo htmlspecialchars($user['name']); ?></div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Email Address</div>
                            <div class="detail-value"><?php echo $user['email']; ?></div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Member Since</div>
                            <div class="detail-value"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></div>
                        </div>
                        
                        <div class="user-stats">
                            <div class="stat-card">
                                <div class="stat-number"><?php echo $order_stats['order_count'] ?: 0; ?></div>
                                <div class="stat-label">Total Orders</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number">$<?php echo number_format($order_stats['total_spent'] ?: 0, 2); ?></div>
                                <div class="stat-label">Total Spent</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- User Orders -->
                    <div class="detail-card">
                        <h3>Order History</h3>
                        <?php if($user_orders->rowCount() > 0): ?>
                            <table class="orders-table">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($order_row = $user_orders->fetch(PDO::FETCH_ASSOC)): ?>
                                    <tr>
                                        <td>#<?php echo $order_row['id']; ?></td>
                                        <td>$<?php echo $order_row['total_amount']; ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo $order_row['status']; ?>">
                                                <?php echo ucfirst($order_row['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($order_row['created_at'])); ?></td>
                                        <td>
                                            <a href="order_details.php?id=<?php echo $order_row['id']; ?>" class="btn btn-sm btn-primary">View</a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="no-orders">
                                <p>This user hasn't placed any orders yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="detail-card" style="margin-top: 2rem;">
                    <h3>Account Actions</h3>
                    <div style="display: flex; gap: 1rem;">
                        <a href="#" class="btn btn-primary">Send Email</a>
                        <a href="#" class="btn btn-warning">Reset Password</a>
                        <a href="users.php?delete=<?php echo $user['id']; ?>" 
                           class="btn btn-danger"
                           onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                            Delete User
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>