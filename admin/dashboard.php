<?php
session_start();
require_once "../config/database.php";
require_once "../classes/Product.php";
require_once "../classes/Order.php";
require_once "../classes/User.php";

// Check admin authentication
if(!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Get statistics
$product = new Product($db);
$order = new Order($db);
$user = new User($db);

// Total products
$total_products = $product->readAll()->rowCount();

// Total orders (you'll need to add this method to Order class)
$total_orders = $order->getAllOrders()->rowCount();

// Total users
$stmt = $db->prepare("SELECT COUNT(*) as total FROM users");
$stmt->execute();
$total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Recent orders
$recent_orders = $order->getAllOrders(5);

// Low stock products
$low_stock = $product->getLowStockProducts();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - StrideRight</title>
    <style>
        :root {
            --sidebar-width: 250px;
            --header-height: 60px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: #2c3e50;
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid #34495e;
        }
        
        .sidebar-header h2 {
            color: #3498db;
        }
        
        .sidebar-menu {
            padding: 1rem 0;
        }
        
        .menu-item {
            padding: 0.8rem 1.5rem;
            color: #bdc3c7;
            text-decoration: none;
            display: block;
            transition: all 0.3s;
        }
        
        .menu-item:hover, .menu-item.active {
            background: #34495e;
            color: white;
            border-left: 4px solid #3498db;
        }
        
        .menu-item i {
            margin-right: 0.5rem;
            width: 20px;
            text-align: center;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
        }
        
        .admin-header {
            background: white;
            padding: 1rem 2rem;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: var(--header-height);
        }
        
        .admin-body {
            padding: 2rem;
            background: #f8f9fa;
            min-height: calc(100vh - var(--header-height));
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #3498db;
        }
        
        .stat-card.products { border-left-color: #e74c3c; }
        .stat-card.orders { border-left-color: #f39c12; }
        .stat-card.users { border-left-color: #27ae60; }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
        
        /* Tables */
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            overflow: hidden;
        }
        
        .card-header {
            padding: 1.5rem;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th, .table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #555;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 0.9rem;
        }
        
        .btn-primary { background: #3498db; color: white; }
        .btn-success { background: #27ae60; color: white; }
        .btn-warning { background: #f39c12; color: white; }
        .btn-danger { background: #e74c3c; color: white; }
        .btn-sm { padding: 0.3rem 0.7rem; font-size: 0.8rem; }
        
        .status-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        .status-pending { background: #fff3cd; color: #856404; }
        .status-completed { background: #d1edff; color: #004085; }
        .status-shipped { background: #d4edda; color: #155724; }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>StrideRight Admin</h2>
            </div>
            <div class="sidebar-menu">
                <a href="dashboard.php" class="menu-item active">Dashboard</a>
                <a href="products.php" class="menu-item">Products</a>
                <a href="orders.php" class="menu-item">Orders</a>
                <a href="users.php" class="menu-item">Users</a>
                <a href="../index.php" class="menu-item">View Store</a>
                <a href="logout.php" class="menu-item">Logout</a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="admin-header">
                <h1>Dashboard</h1>
                <div>Welcome, Admin!</div>
            </div>
            
            <div class="admin-body">
                <!-- Statistics -->
                <div class="stats-grid">
                    <div class="stat-card products">
                        <div class="stat-number"><?php echo $total_products; ?></div>
                        <div class="stat-label">Total Products</div>
                    </div>
                    <div class="stat-card orders">
                        <div class="stat-number"><?php echo $total_orders; ?></div>
                        <div class="stat-label">Total Orders</div>
                    </div>
                    <div class="stat-card users">
                        <div class="stat-number"><?php echo $total_users; ?></div>
                        <div class="stat-label">Registered Users</div>
                    </div>
                </div>
                
                <!-- Recent Orders -->
                <div class="card">
                    <div class="card-header">
                        <h3>Recent Orders</h3>
                        <a href="orders.php" class="btn btn-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($order_row = $recent_orders->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td>#<?php echo $order_row['id']; ?></td>
                                    <td>User #<?php echo $order_row['user_id']; ?></td>
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
                    </div>
                </div>
                
                <!-- Low Stock Products -->
                <div class="card">
                    <div class="card-header">
                        <h3>Low Stock Products</h3>
                        <a href="products.php" class="btn btn-primary">Manage Products</a>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Stock</th>
                                    <th>Price</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($product_row = $low_stock->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td><?php echo $product_row['name']; ?></td>
                                    <td><?php echo $product_row['category']; ?></td>
                                    <td>
                                        <span style="color: <?php echo $product_row['stock'] < 5 ? '#e74c3c' : '#f39c12'; ?>">
                                            <?php echo $product_row['stock']; ?>
                                        </span>
                                    </td>
                                    <td>$<?php echo $product_row['price']; ?></td>
                                    <td>
                                        <a href="edit_product.php?id=<?php echo $product_row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
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