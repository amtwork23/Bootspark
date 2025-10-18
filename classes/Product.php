<?php
class Product {
    private $conn;
    private $table_name = "products";
    
    public $id;
    public $name;
    public $description;
    public $price;
    public $image;
    public $category;
    public $stock;
    public $created_at;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Read all products
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    // Read single product
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->price = $row['price'];
            $this->image = $row['image'];
            $this->category = $row['category'];
            $this->stock = $row['stock'];
            return true;
        }
        return false;
    }
    
    // Get products by category
    public function readByCategory($category) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE category = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $category);
        $stmt->execute();
        return $stmt;
    }
}
?>