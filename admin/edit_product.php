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

// Get product ID from URL
$product_id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Product ID not found.');

// Set product ID
$product->id = $product_id;

// Read the product details
$product->readOne();

// Handle form submission
if($_POST) {
    $product->name = $_POST['name'];
    $product->description = $_POST['description'];
    $product->price = $_POST['price'];
    $product->category = $_POST['category'];
    $product->stock = $_POST['stock'];
    $product->image = $_POST['image'];
    
    if($product->update()) {
        $message = "Product updated successfully!";
        // Re-read the product to get updated values
        $product->readOne();
    } else {
        $error = "Unable to update product.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - StrideRight Admin</title>
    <style>
        .form-container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 0 auto;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }
        
        .form-group input, 
        .form-group select, 
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .form-group textarea {
            height: 120px;
            resize: vertical;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .product-preview {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .product-preview img {
            max-width: 200px;
            height: auto;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        
        .btn-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 1rem;
        }
        
        .btn-primary { background: #3498db; color: white; }
        .btn-secondary { background: #95a5a6; color: white; }
        .btn-success { background: #27ae60; color: white; }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        
        <div class="main-content">
            <div class="admin-header">
                <h1>Edit Product</h1>
                <a href="products.php" class="btn btn-secondary">← Back to Products</a>
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
                
                <div class="form-container">
                    <div class="product-preview">
                        <img src="../assets/images/<?php echo $product->image; ?>" 
                             alt="<?php echo $product->name; ?>"
                             onerror="this.src='https://via.placeholder.com/200x200?text=No+Image'">
                        <p>Current Image: <?php echo $product->image; ?></p>
                    </div>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label>Product Name *</label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($product->name); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Description *</label>
                            <textarea name="description" required><?php echo htmlspecialchars($product->description); ?></textarea>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Price ($) *</label>
                                <input type="number" name="price" step="0.01" value="<?php echo $product->price; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Stock Quantity *</label>
                                <input type="number" name="stock" value="<?php echo $product->stock; ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Category *</label>
                                <select name="category" required>
                                    <option value="Running" <?php echo $product->category == 'Running' ? 'selected' : ''; ?>>Running</option>
                                    <option value="Casual" <?php echo $product->category == 'Casual' ? 'selected' : ''; ?>>Casual</option>
                                    <option value="Boots" <?php echo $product->category == 'Boots' ? 'selected' : ''; ?>>Boots</option>
                                    <option value="Formal" <?php echo $product->category == 'Formal' ? 'selected' : ''; ?>>Formal</option>
                                    <option value="Sports" <?php echo $product->category == 'Sports' ? 'selected' : ''; ?>>Sports</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Image Filename *</label>
                                <input type="text" name="image" value="<?php echo htmlspecialchars($product->image); ?>" required>
                                <small style="color: #666;">e.g., nike_air_max.jpg</small>
                            </div>
                        </div>
                        
                        <div class="btn-group">
                            <button type="submit" class="btn btn-success">Update Product</button>
                            <a href="products.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>