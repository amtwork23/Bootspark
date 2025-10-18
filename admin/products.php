<?php
session_start();
require_once "../config/database.php";
require_once "../classes/Product.php";

if(!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$product = new Product($db);

// Handle product actions
if(isset($_POST['add_product'])) {
    // Add new product
    $product->name = $_POST['name'];
    $product->description = $_POST['description'];
    $product->price = $_POST['price'];
    $product->category = $_POST['category'];
    $product->stock = $_POST['stock'];
    $product->image = $_POST['image'];
    
    if($product->create()) {
        $message = "Product added successfully!";
    } else {
        $error = "Failed to add product.";
    }
}

if(isset($_GET['delete'])) {
    // Delete product
    $product->id = $_GET['delete'];
    if($product->delete()) {
        $message = "Product deleted successfully!";
    } else {
        $error = "Failed to delete product.";
    }
}

$products = $product->readAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - StrideRight Admin</title>
    <style>
        /* Add to existing admin styles */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 2rem;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .form-group input, .form-group select, .form-group textarea {
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
        
        .close {
            float: right;
            font-size: 1.5rem;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        
        <div class="main-content">
            <div class="admin-header">
                <h1>Manage Products</h1>
                <button onclick="openModal()" class="btn btn-success">Add New Product</button>
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
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $products->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td>
                                        <img src="../assets/images/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" 
                                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;" 
                                             onerror="this.src='https://via.placeholder.com/50x50?text=No+Image'">
                                    </td>
                                    <td><?php echo $row['name']; ?></td>
                                    <td><?php echo $row['category']; ?></td>
                                    <td>$<?php echo $row['price']; ?></td>
                                    <td>
                                        <span style="color: <?php echo $row['stock'] < 10 ? '#e74c3c' : '#27ae60'; ?>">
                                            <?php echo $row['stock']; ?>
                                        </span>
                                    </td>
                                    <td class="action-buttons">
                                        <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <a href="products.php?delete=<?php echo $row['id']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
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
    
    <!-- Add Product Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Add New Product</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" required></textarea>
                </div>
                <div class="form-group">
                    <label>Price</label>
                    <input type="number" name="price" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category" required>
                        <option value="Running">Running</option>
                        <option value="Casual">Casual</option>
                        <option value="Boots">Boots</option>
                        <option value="Formal">Formal</option>
                        <option value="Sports">Sports</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Stock Quantity</label>
                    <input type="number" name="stock" required>
                </div>
                <div class="form-group">
                    <label>Image URL</label>
                    <input type="text" name="image" placeholder="image.jpg" required>
                </div>
                <button type="submit" name="add_product" class="btn btn-success">Add Product</button>
            </form>
        </div>
    </div>
    
    <script>
        function openModal() {
            document.getElementById('productModal').style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('productModal').style.display = 'none';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            var modal = document.getElementById('productModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>