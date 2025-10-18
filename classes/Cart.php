<?php
class Cart {
    protected $conn;
    private $table_name = "cart_items";
    
    public $id;
    public $user_id;
    public $product_id;
    public $quantity;
    public $created_at;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Add to cart
    public function addToCart() {
        $query = "SELECT id, quantity FROM " . $this->table_name . " 
                 WHERE user_id = ? AND product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_id);
        $stmt->bindParam(2, $this->product_id);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $query = "UPDATE " . $this->table_name . " 
                     SET quantity = quantity + ? 
                     WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->quantity);
            $stmt->bindParam(2, $row['id']);
        } else {
            $query = "INSERT INTO " . $this->table_name . " 
                     SET user_id=:user_id, product_id=:product_id, quantity=:quantity";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $this->user_id);
            $stmt->bindParam(":product_id", $this->product_id);
            $stmt->bindParam(":quantity", $this->quantity);
        }
        
        return $stmt->execute();
    }
    
    // Get cart items for user
    public function getCartItems($user_id) {
        $query = "SELECT c.*, p.name, p.price, p.image 
                 FROM " . $this->table_name . " c 
                 JOIN products p ON c.product_id = p.id 
                 WHERE c.user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        return $stmt;
    }
    
    // Remove from cart
    public function removeFromCart() {
        $query = "DELETE FROM " . $this->table_name . " 
                 WHERE user_id = ? AND product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_id);
        $stmt->bindParam(2, $this->product_id);
        return $stmt->execute();
    }
    
    // Clear cart
    public function clearCart($user_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        return $stmt->execute();
    }
    
    // Update cart quantity
    public function updateQuantity() {
        $query = "UPDATE " . $this->table_name . " 
                 SET quantity = ? 
                 WHERE user_id = ? AND product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->quantity);
        $stmt->bindParam(2, $this->user_id);
        $stmt->bindParam(3, $this->product_id);
        return $stmt->execute();
    }
}
?>