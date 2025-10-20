<?php
require_once 'Database.php';

class Product {
    private $db;
    private $id;
    private $name;
    private $slug;
    private $description;
    private $price;
    private $image;
    private $stock;
    private $created_at;
    
    public function __construct($database = null) {
        $this->db = $database ?: new Database();
    }
    
    public function getAllProducts() {
        $result = $this->db->query("SELECT * FROM products ORDER BY created_at DESC");
        $products = [];
        
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        
        return $products;
    }
    
    public function getActiveProducts() {
        $result = $this->db->query("SELECT * FROM products WHERE status = 'active' ORDER BY created_at DESC");
        $products = [];
        
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        
        return $products;
    }
    
    public function getProductById($id) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->slug = $row['slug'];
            $this->description = $row['description'];
            $this->price = $row['price'];
            $this->image = $row['image'];
            $this->stock = $row['stock'];
            $this->created_at = $row['created_at'];
            
            return $row;
        }
        
        return false;
    }
    
    public function getProductsByIds($ids) {
        if (empty($ids)) return [];
        
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
        $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        
        return $products;
    }
    
    public function updateStock($product_id, $quantity) {
        $stmt = $this->db->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $stmt->bind_param('ii', $quantity, $product_id);
        return $stmt->execute();
    }
    
    public function addProduct($name, $slug, $description, $price, $image, $stock = 0, $category = 'general', $status = 'active') {
        $stmt = $this->db->prepare("INSERT INTO products (name, slug, description, price, image, stock, category, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sssdsiss', $name, $slug, $description, $price, $image, $stock, $category, $status);
        
        if ($stmt->execute()) {
            $this->id = $this->db->getLastInsertId();
            return $this->id;
        }
        
        return false;
    }
    
    public function updateProduct($id, $name, $slug, $description, $price, $image, $stock, $category = 'general', $status = 'active') {
        $stmt = $this->db->prepare("UPDATE products SET name = ?, slug = ?, description = ?, price = ?, image = ?, stock = ?, category = ?, status = ? WHERE id = ?");
        $stmt->bind_param('sssdsissi', $name, $slug, $description, $price, $image, $stock, $category, $status, $id);
        return $stmt->execute();
    }
    
    public function deleteProduct($id) {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getSlug() { return $this->slug; }
    public function getDescription() { return $this->description; }
    public function getPrice() { return $this->price; }
    public function getImage() { return $this->image; }
    public function getStock() { return $this->stock; }
    public function getCreatedAt() { return $this->created_at; }
}
?>
