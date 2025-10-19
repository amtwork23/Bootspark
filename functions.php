<?php
// functions.php
function getProductImage($productName) {
    // Check if local image exists first
    $localImagePath = "assets/images/" . strtolower(str_replace(' ', '_', $productName)) . ".jpg";
    if(file_exists($localImagePath)) {
        return $localImagePath;
    }
    
    $imageMap = [
        // Original Products
        'Nike Air Max' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60',
        'Adidas Ultraboost' => 'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60',
        'Clarks Desert Boot' => 'https://images.unsplash.com/photo-1529810413229-65faf7617d6a?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60',
        'Timberland Premium' => 'https://images.unsplash.com/photo-1520006403909-838d6b92c22e?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60',
        'Vans Old Skool' => 'https://images.unsplash.com/photo-1549298916-f52d724204b4?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60',
        'New Balance 574' => 'https://images.unsplash.com/photo-1549289524-06cf8837ace5?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60',
        'Nike Air Force 1' => 'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60',
        'Adidas Stan Smith' => 'https://images.unsplash.com/photo-1605348532760-6753d2c43329?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60',
        
        // NEW RUNNING SHOES
        'Nike Pegasus 38' => 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60',
        'Asics Gel-Kayano 28' => 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60',
        'Brooks Ghost 14' => 'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60',
        'Hoka One One Clifton 8' => 'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60',
        'Saucony Endorphin Speed' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60',
        
        // NEW CASUAL SHOES
        'Converse Chuck Taylor' => 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60',
        'Puma Suede Classic' => 'https://images.unsplash.com/photo-1560769624-6a4f1f3c7297?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60',
        'Reebok Classic Leather' => 'https://images.unsplash.com/photo-1575537302964-96f9479c4aad?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60',
        'Skechers Relaxed Fit' => 'https://images.unsplash.com/photo-1551107696-a4b0c5a0d9a2?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60',
        'Tommy Hilfiger Sneakers' => 'https://images.unsplash.com/photo-1543508282-6319a3e2621f?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60',
        
        // NEW BOOTS
        'Dr. Martens 1460' => 'https://images.unsplash.com/photo-1520006403909-838d6b92c22e?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60',
        'Red Wing Iron Ranger' => 'https://images.unsplash.com/photo-1529810413229-65faf7617d6a?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60',
        'CAT Colorado Boots' => 'https://images.unsplash.com/photo-1542280756-74b2f55e73ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60',
        'Wolverine DuraShocks' => 'https://images.unsplash.com/photo-1520006403909-838d6b92c22e?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60',
        
        // NEW FORMAL SHOES
        'Allen Edmonds Park Avenue' => 'https://images.unsplash.com/photo-1520637836861-8cefbd5c1e93?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60',
        'Cole Haan GrandPro' => 'https://images.unsplash.com/photo-1534452203293-494d7ddbf7e0?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60',
        'Johnston & Murphy Capstone' => 'https://images.unsplash.com/photo-1520637836861-8cefbd5c1e93?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60',
        'Florsheim Lexington' => 'https://images.unsplash.com/photo-1534452203293-494d7ddbf7e0?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60',
        
        // NEW SPORTS SHOES
        'Nike Metcon 7' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60',
        'Adidas Powerlift 4' => 'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60',
        'Reebok Nano X1' => 'https://images.unsplash.com/photo-1575537302964-96f9479c4aad?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60',
        'Under Armour Tribase Reign' => 'https://images.unsplash.com/photo-1543508282-6319a3e2621f?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60'
    ];
    
    return $imageMap[$productName] ?? 'https://via.placeholder.com/300x200?text=No+Image';
}
?>