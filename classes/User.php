<?php
require_once 'Database.php';

class User {
    private $db;
    private $id;
    private $name;
    private $email;
    private $password;
    private $role;
    
    public function __construct($database = null) {
        $this->db = $database ?: new Database();
    }
    
    public function register($name, $email, $password, $confirm_password) {
        $errors = [];
        
        if (empty($name)) $errors[] = "Name is required.";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Provide a valid email.";
        if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";
        if ($password !== $confirm_password) $errors[] = "Passwords do not match.";
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        // Check if email already exists
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            return ['success' => false, 'errors' => ['Email already registered.']];
        }
        
        // Create new user (default role is 'user')
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $role = 'user';
        $stmt = $this->db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $name, $email, $hashed_password, $role);
        
        if ($stmt->execute()) {
            $this->id = $this->db->getLastInsertId();
            $this->name = $name;
            $this->email = $email;
            
            return ['success' => true, 'user_id' => $this->id, 'user_name' => $name];
        } else {
            return ['success' => false, 'errors' => ['Registration failed, try again.']];
        }
    }
    
    public function login($email, $password) {
        $errors = [];
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Enter a valid email.";
        if (empty($password)) $errors[] = "Enter password.";
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        $stmt = $this->db->prepare("SELECT id, name, password, role FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->email = $email;
                $this->role = $row['role'];
                
                return ['success' => true, 'user_id' => $row['id'], 'user_name' => $row['name'], 'role' => $row['role']];
            } else {
                return ['success' => false, 'errors' => ['Incorrect password.']];
            }
        } else {
            return ['success' => false, 'errors' => ['No account with that email.']];
        }
    }
    
    public function getUserById($user_id) {
        $stmt = $this->db->prepare("SELECT id, name, email FROM users WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->email = $row['email'];
            return $row;
        }
        
        return false;
    }
    
    public function getOrderCount($user_id) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as cnt FROM orders WHERE user_id = ?");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['cnt'] ?? 0;
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getEmail() { return $this->email; }
    public function getRole() { return $this->role; }
}
?>
