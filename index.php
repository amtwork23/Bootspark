<?php
session_start();
require_once "config/database.php";
require_once "classes/Product.php";
require_once "functions.php";

$database = new Database();
$db = $database->getConnection();
$product = new Product($db);
$products = $product->readAll();
?>
<?php include 'header.php'; ?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Step Into Style & Comfort</h1>
        <p>Discover our premium collection of men's shoes designed for every occasion. From casual to formal, we've got you covered.</p>
        <a href="products.php"><button class="cta-button">Shop Now</button></a>
    </div>
</section>

<!-- Featured Products -->
<section class="products">
    <div class="container">
        <h2 class="section-title">Featured Products</h2>
        <div class="product-grid">
            <?php while($row = $products->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="product-card">
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
</section>
<style>
    /* Hero Section */
    .hero {
        background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1549298916-b41d501d3772?ixlib=rb-1.2.1&auto=format&fit=crop&w=1200&q=80');
        background-size: cover;
        background-position: center;
        color: white;
        padding: 5rem 0;
        text-align: center;
    }
    
    .hero h1 {
        font-size: 3rem;
        margin-bottom: 1rem;
    }
    
    .hero p {
        font-size: 1.2rem;
        max-width: 600px;
        margin: 0 auto 2rem;
    }
    
    .cta-button {
        background: var(--accent);
        color: white;
        border: none;
        padding: 0.8rem 2rem;
        font-size: 1.1rem;
        border-radius: 4px;
        cursor: pointer;
        transition: background 0.3s;
    }
    
    .cta-button:hover {
        background: #c0392b;
    }
    
    /* Products Section */
    .products {
        padding: 4rem 0;
    }
    
    .section-title {
        text-align: center;
        margin-bottom: 3rem;
        font-size: 2rem;
        color: var(--dark);
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
    }
    
    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
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
    
    .product-description {
        color: #666;
        margin-bottom: 1rem;
        font-size: 0.9rem;
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
    }
    
    .add-to-cart:hover {
        background: #2980b9;
    }
    
</style>

<?php include 'footer.php'; ?>