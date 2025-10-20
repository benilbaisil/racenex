<?php
require_once 'Database.php';
require_once 'Product.php';

class Order {
    private $db;
    
    public function __construct($database = null) {
        $this->db = $database ?: new Database();
    }
    
    public function updateOrderStatus($order_id, $status) {
        $stmt = $this->db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param('si', $status, $order_id);
        return $stmt->execute();
    }
    
    public function getOrderById($order_id) {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return $row;
        }
        
        return false;
    }
    
    public function getAllOrders() {
        $result = $this->db->query("
            SELECT o.*, u.name as user_name 
            FROM orders o 
            LEFT JOIN users u ON o.user_id = u.id 
            ORDER BY o.created_at DESC
        ");
        
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        
        return $orders;
    }
    
    /**
     * Return all orders for a specific user, most recent first
     * Includes only this user's rows from `orders` table
     */
    public function getUserOrders(int $userId) {
        $stmt = $this->db->prepare(
            "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC"
        );
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        
        return $orders;
    }
    
    public function getOrderItems($order_id) {
        $stmt = $this->db->prepare("
            SELECT oi.*, p.name as product_name, p.price as product_price
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $stmt->bind_param('i', $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        
        return $items;
    }
    
    public function createOrderFromCartData($user_id, $cart_items, $address, $payment_method = 'Razorpay', $payment_id = '') {
        try {
            // Start transaction
            $this->db->getConnection()->autocommit(false);
            
            // Calculate total
            $total = 0;
            $product = new Product();
            
            foreach ($cart_items as $product_id => $quantity) {
                $product_data = $product->getProductById($product_id);
                if ($product_data) {
                    $total += $product_data['price'] * $quantity;
                }
            }
            
            // Create order
            $stmt = $this->db->prepare("
                INSERT INTO orders (user_id, address, total, payment_method, payment_id, status) 
                VALUES (?, ?, ?, ?, ?, 'pending')
            ");
            $stmt->bind_param('issss', $user_id, $address, $total, $payment_method, $payment_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to create order");
            }
            
            $order_id = $this->db->getLastInsertId();
            
            // Create order items
            $stmt = $this->db->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, price) 
                VALUES (?, ?, ?, ?)
            ");
            
            foreach ($cart_items as $product_id => $quantity) {
                $product_data = $product->getProductById($product_id);
                if ($product_data) {
                    $stmt->bind_param('iiid', $order_id, $product_id, $quantity, $product_data['price']);
                    if (!$stmt->execute()) {
                        throw new Exception("Failed to create order item");
                    }
                    
                    // Update product stock
                    $product->updateStock($product_id, $quantity);
                }
            }
            
            // Commit transaction
            $this->db->getConnection()->commit();
            $this->db->getConnection()->autocommit(true);
            
            return ['success' => true, 'order_id' => $order_id, 'message' => 'Order created successfully'];
            
        } catch (Exception $e) {
            // Rollback transaction
            $this->db->getConnection()->rollback();
            $this->db->getConnection()->autocommit(true);
            
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
?>