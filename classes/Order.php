<?php
require_once 'Cart.php';

class Order extends Cart {
    private $order_table = "orders";
    private $order_items_table = "order_items";
    
    public $order_id;
    public $total_amount;
    public $status;
    public $payment_id;
    public $address;
    
    public function __construct($db) {
        parent::__construct($db);
    }
    
    // Create order
    public function createOrder() {
        try {
            $this->conn->beginTransaction();
            
            $query = "INSERT INTO " . $this->order_table . " 
                     SET user_id=:user_id, total_amount=:total_amount, 
                         address=:address, status='pending'";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $this->user_id);
            $stmt->bindParam(":total_amount", $this->total_amount);
            $stmt->bindParam(":address", $this->address);
            $stmt->execute();
            
            $this->order_id = $this->conn->lastInsertId();
            
            $cart_items = $this->getCartItems($this->user_id);
            
            while($row = $cart_items->fetch(PDO::FETCH_ASSOC)) {
                $query = "INSERT INTO " . $this->order_items_table . " 
                         SET order_id=:order_id, product_id=:product_id, 
                             quantity=:quantity, price=:price";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(":order_id", $this->order_id);
                $stmt->bindParam(":product_id", $row['product_id']);
                $stmt->bindParam(":quantity", $row['quantity']);
                $stmt->bindParam(":price", $row['price']);
                $stmt->execute();
            }
            
            $this->clearCart($this->user_id);
            
            $this->conn->commit();
            return true;
            
        } catch(Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
    
    // Update payment status
    public function updatePaymentStatus($payment_id, $status) {
        $query = "UPDATE " . $this->order_table . " 
                 SET payment_id=:payment_id, status=:status 
                 WHERE id=:order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":payment_id", $payment_id);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":order_id", $this->order_id);
        return $stmt->execute();
    }
    
    // Get user orders
    public function getUserOrders($user_id) {
        $query = "SELECT * FROM " . $this->order_table . " 
                 WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        return $stmt;
    }
    // Verify payment and create order
public function createOrderWithPayment($payment_data) {
    try {
        $this->conn->beginTransaction();
        
        // Create order
        $query = "INSERT INTO " . $this->order_table . " 
                 SET user_id=:user_id, total_amount=:total_amount, 
                     address=:address, status='pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":total_amount", $this->total_amount);
        $stmt->bindParam(":address", $this->address);
        $stmt->execute();
        
        $this->order_id = $this->conn->lastInsertId();
        
        // Add order items
        $cart_items = $this->getCartItems($this->user_id);
        while($row = $cart_items->fetch(PDO::FETCH_ASSOC)) {
            $query = "INSERT INTO " . $this->order_items_table . " 
                     SET order_id=:order_id, product_id=:product_id, 
                         quantity=:quantity, price=:price";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":order_id", $this->order_id);
            $stmt->bindParam(":product_id", $row['product_id']);
            $stmt->bindParam(":quantity", $row['quantity']);
            $stmt->bindParam(":price", $row['price']);
            $stmt->execute();
        }
        
        // Update payment status
        $this->updatePaymentStatus($payment_data['razorpay_payment_id'], 'completed');
        
        // Clear cart
        $this->clearCart($this->user_id);
        
        $this->conn->commit();
        return true;
        
    } catch(Exception $e) {
        $this->conn->rollBack();
        error_log("Order creation failed: " . $e->getMessage());
        return false;
    }
}
}

?>