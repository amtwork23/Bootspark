<?php
// Debug script to check product images
require_once "config/database.php";
require_once "functions.php";

$database = new Database();
$db = $database->getConnection();

// Get all products from database
$query = "SELECT id, name, category FROM products ORDER BY name";
$stmt = $db->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>Product Image Debug</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>ID</th><th>Product Name</th><th>Category</th><th>Image URL</th><th>Status</th></tr>";

foreach($products as $product) {
    $imageUrl = getProductImage($product['name']);
    $hasImage = strpos($imageUrl, 'placeholder') === false;
    $status = $hasImage ? '✅ Has Image' : '❌ No Image';
    
    echo "<tr>";
    echo "<td>{$product['id']}</td>";
    echo "<td>{$product['name']}</td>";
    echo "<td>{$product['category']}</td>";
    echo "<td style='max-width: 300px; word-wrap: break-word;'>{$imageUrl}</td>";
    echo "<td>{$status}</td>";
    echo "</tr>";
}

echo "</table>";

// Check for products without images
$productsWithoutImages = [];
foreach($products as $product) {
    $imageUrl = getProductImage($product['name']);
    if(strpos($imageUrl, 'placeholder') !== false) {
        $productsWithoutImages[] = $product['name'];
    }
}

if(!empty($productsWithoutImages)) {
    echo "<h3>Products without images:</h3>";
    echo "<ul>";
    foreach($productsWithoutImages as $productName) {
        echo "<li>{$productName}</li>";
    }
    echo "</ul>";
} else {
    echo "<h3>✅ All products have images!</h3>";
}
?>

