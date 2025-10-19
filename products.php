<?php
session_start();
require_once "config/database.php";
require_once "classes/Product.php";
require_once "functions.php";

$database = new Database();
$db = $database->getConnection();
if($db === null) {
    die("Database connection failed");
}
$product = new Product($db);

$category = isset($_GET['category']) ? $_GET['category'] : '';
if($category) {
    $products = $product->readByCategory($category);
} else {
    $products = $product->readAll();
}

$categories = ['Running', 'Casual', 'Boots', 'Formal', 'Sports'];
?>
<?php include 'header.php'; ?>

<div class="container">
    <div class="products-page">
        <h1>Our Collection</h1>
        
        <div class="filter-section">
            <h3>Filter by Category</h3>
            <div class="category-filters">
                <a href="products.php" class="category-btn <?php echo !$category ? 'active' : ''; ?>">All</a>
                <?php foreach($categories as $cat): ?>
                <a href="products.php?category=<?php echo urlencode($cat); ?>" 
                   class="category-btn <?php echo $category == $cat ? 'active' : ''; ?>">
                    <?php echo $cat; ?>
                </a>
                <?php endforeach; ?>
            </div>
            
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search products by name...">
            </div>
        </div>
        
        <div class="product-grid">
            <?php while($row = $products->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="product-card" data-name="<?php echo strtolower($row['name']); ?>">
                <div class="product-image">
                    <!-- UPDATED IMAGE CODE WITH ERROR HANDLING -->
                    <img src="<?php echo getProductImage($row['name']); ?>" 
                         alt="<?php echo $row['name']; ?>" 
                         onerror="this.src='https://via.placeholder.com/300x200?text=Image+Not+Available'; this.onerror=null;"
                         loading="lazy">
                </div>
                <div class="product-info">
                    <h3 class="product-name"><?php echo $row['name']; ?></h3>
                    <p class="product-price">₹<?php echo number_format($row['price'], 2); ?></p>
                    <p class="product-category" style="color: #666; font-size: 0.9rem;"><?php echo $row['category']; ?></p>
                    <p class="product-description"><?php echo substr($row['description'], 0, 100); ?>...</p>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <form action="cart.php" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
                        </form>
                    <?php else: ?>
                        <a href="login.php"><button class="add-to-cart">Login to Purchase</button></a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>
<style>
    .products-page {
        padding: 2rem 0;
    }
    
    .filter-section {
        background: white;
        padding: 1.5rem;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }
    
    .category-filters {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    .category-btn {
        padding: 0.5rem 1rem;
        background: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
        color: #333;
        transition: all 0.3s;
    }
    
    .category-btn:hover, .category-btn.active {
        background: var(--secondary);
        color: white;
        border-color: var(--secondary);
    }
    
    .search-box {
        margin-top: 1rem;
    }
    
    .search-box input {
        padding: 0.8rem;
        width: 300px;
        max-width: 100%;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 2rem;
    }
    
    .product-card {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }
    
    .product-image {
        height: 200px;
        background-color: #f5f5f5;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
    }
    
    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        transition: opacity 0.3s ease;
    }
    
    .product-image img[src*="placeholder"] {
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    .product-info {
        padding: 1.5rem;
    }
    
    .product-name {
        font-size: 1.2rem;
        margin-bottom: 0.5rem;
        color: var(--dark);
    }
    
    .product-price {
        font-size: 1.3rem;
        font-weight: bold;
        color: var(--accent);
        margin-bottom: 1rem;
    }
    
    .product-category {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }
    
    .product-description {
        color: #666;
        margin-bottom: 1rem;
        font-size: 0.9rem;
        line-height: 1.4;
    }
    
    .add-to-cart {
        background: var(--secondary);
        color: white;
        border: none;
        padding: 0.7rem 1.5rem;
        border-radius: 4px;
        cursor: pointer;
        width: 100%;
        transition: background 0.3s;
        font-size: 0.9rem;
    }
    
    .add-to-cart:hover {
        background: #2980b9;
    }
</style>

<script>
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const productCards = document.querySelectorAll('.product-card');
        
        productCards.forEach(card => {
            const productName = card.getAttribute('data-name');
            if (productName.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>

<?php include 'footer.php'; ?>