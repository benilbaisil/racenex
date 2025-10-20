<?php
require_once 'Database.php';
require_once 'Product.php';

class Cart {
    private $db;
    private $product;
    private $session_cart;
    
    public function __construct($database = null) {
        $this->db = $database ?: new Database();
        $this->product = new Product($this->db);
        $this->initCart();
    }
    
    private function initCart() {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        $this->session_cart = &$_SESSION['cart'];
    }
    
    public function addToCart($product_id, $quantity = 1) {
        $product_id = (int)$product_id;
        $quantity = (int)$quantity;
        
        if ($product_id <= 0 || $quantity <= 0) {
            return ['success' => false, 'message' => 'Invalid product or quantity'];
        }
        
        // Check if product exists and is active
        $product = $this->product->getProductById($product_id);
        if (!$product) {
            return ['success' => false, 'message' => 'Product not found'];
        }
        
        // Check if product is active
        if (($product['status'] ?? 'active') !== 'active') {
            return ['success' => false, 'message' => 'Product is not available'];
        }
        
        // Check stock availability
        if ($product['stock'] < ($this->session_cart[$product_id] ?? 0) + $quantity) {
            return ['success' => false, 'message' => 'Insufficient stock'];
        }
        
        if (isset($this->session_cart[$product_id])) {
            $this->session_cart[$product_id] += $quantity;
        } else {
            $this->session_cart[$product_id] = $quantity;
        }
        
        return ['success' => true, 'message' => 'Product added to cart'];
    }
    
    public function removeFromCart($product_id) {
        $product_id = (int)$product_id;
        
        if (isset($this->session_cart[$product_id])) {
            unset($this->session_cart[$product_id]);
            return ['success' => true, 'message' => 'Product removed from cart'];
        }
        
        return ['success' => false, 'message' => 'Product not in cart'];
    }
    
    public function updateCart($quantities) {
        $updated = false;
        
        foreach ($quantities as $product_id => $quantity) {
            $product_id = (int)$product_id;
            $quantity = (int)$quantity;
            
            if ($quantity <= 0) {
                if (isset($this->session_cart[$product_id])) {
                    unset($this->session_cart[$product_id]);
                    $updated = true;
                }
            } else {
                // Check stock availability
                $product = $this->product->getProductById($product_id);
                if ($product && $product['stock'] >= $quantity) {
                    $this->session_cart[$product_id] = $quantity;
                    $updated = true;
                }
            }
        }
        
        return ['success' => $updated, 'message' => $updated ? 'Cart updated' : 'No changes made'];
    }
    
    public function getCartItems() {
        if (empty($this->session_cart)) {
            return ['items' => [], 'total' => 0, 'total_items' => 0];
        }
        
        $product_ids = array_keys($this->session_cart);
        $products = $this->product->getProductsByIds($product_ids);
        
        $cart_items = [];
        $total = 0.0;
        $total_items = 0;
        
        foreach ($products as $product) {
            $quantity = $this->session_cart[$product['id']];
            $subtotal = $quantity * $product['price'];
            
            $cart_items[] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $product['image'],
                'stock' => $product['stock'],
                'quantity' => $quantity,
                'subtotal' => $subtotal
            ];
            
            $total += $subtotal;
            $total_items += $quantity;
        }
        
        return [
            'items' => $cart_items,
            'total' => $total,
            'total_items' => $total_items
        ];
    }
    
    public function getCartTotal() {
        $cart_data = $this->getCartItems();
        return $cart_data['total'];
    }
    
    public function getCartItemCount() {
        $cart_data = $this->getCartItems();
        return $cart_data['total_items'];
    }
    
    public function clearCart() {
        $this->session_cart = [];
        $_SESSION['cart'] = [];
        return ['success' => true, 'message' => 'Cart cleared'];
    }
    
    public function isEmpty() {
        return empty($this->session_cart);
    }
    
    public function getCartData() {
        return $this->session_cart;
    }
    
    public function setCartData($cart_data) {
        $this->session_cart = $cart_data;
        $_SESSION['cart'] = $cart_data;
    }
}
?>
