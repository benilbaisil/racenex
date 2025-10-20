<?php
require_once 'Database.php';
require_once 'Product.php';

class Admin {
    private $db;
    private $id;
    private $name;
    private $email;
    private $role;
    
    public function __construct($database = null) {
        $this->db = $database ?: new Database();
    }
    
    public function login($email, $password) {
        $errors = [];
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Enter a valid email.";
        if (empty($password)) $errors[] = "Enter password.";
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        $stmt = $this->db->prepare("SELECT id, name, email, password, role FROM users WHERE email = ? AND role = 'admin' LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->email = $row['email'];
                $this->role = $row['role'];
                
                return ['success' => true, 'admin_id' => $row['id'], 'admin_name' => $row['name'], 'role' => $row['role']];
            } else {
                return ['success' => false, 'errors' => ['Incorrect password.']];
            }
        } else {
            return ['success' => false, 'errors' => ['No admin account with that email.']];
        }
    }
    
    public function getAllUsers() {
        $result = $this->db->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC");
        $users = [];
        
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        
        return $users;
    }
    
    public function getUserCount() {
        $result = $this->db->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'");
        return $result->fetch_assoc()['count'];
    }
    
    public function getOrderCount() {
        $result = $this->db->query("SELECT COUNT(*) as count FROM orders");
        return $result->fetch_assoc()['count'];
    }
    
    public function getProductCount() {
        $result = $this->db->query("SELECT COUNT(*) as count FROM products");
        return $result->fetch_assoc()['count'];
    }
    
    public function getTotalRevenue() {
        $result = $this->db->query("SELECT SUM(total) as total FROM orders WHERE status = 'completed'");
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }
    
    public function getRecentOrders($limit = 10) {
        $stmt = $this->db->prepare("
            SELECT o.*, u.name as user_name 
            FROM orders o 
            LEFT JOIN users u ON o.user_id = u.id 
            ORDER BY o.created_at DESC 
            LIMIT ?
        ");
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        
        return $orders;
    }
    
    public function getProductStats() {
        $result = $this->db->query("
            SELECT 
                COUNT(*) as total_products,
                SUM(stock) as total_stock,
                AVG(price) as avg_price,
                COUNT(CASE WHEN status = 'active' THEN 1 END) as active_products
            FROM products
        ");
        return $result->fetch_assoc();
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getEmail() { return $this->email; }
    public function getRole() { return $this->role; }
}
?>
