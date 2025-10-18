<?php
// Reusable sidebar component for admin pages
?>
<div class="sidebar">
    <div class="sidebar-header">
        <h2>StrideRight Admin</h2>
    </div>
    <div class="sidebar-menu">
        <a href="dashboard.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
            📊 Dashboard
        </a>
        <a href="products.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">
            👟 Products
        </a>
        <a href="orders.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>">
            📦 Orders
        </a>
        <a href="users.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
            👥 Users
        </a>
        <a href="../index.php" class="menu-item" target="_blank">
            🏪 View Store
        </a>
        <a href="logout.php" class="menu-item">
            🚪 Logout
        </a>
    </div>
</div>