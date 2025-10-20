<?php
require_once 'Admin.php';

class AdminSessionManager {
    public static function start() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public static function login($admin_id, $admin_name, $role) {
        self::start();
        
        // Clear any existing regular user session to avoid conflicts
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        
        $_SESSION['admin_id'] = $admin_id;
        $_SESSION['admin_name'] = $admin_name;
        $_SESSION['admin_role'] = $role;
        $_SESSION['admin_logged_in'] = true;
    }
    
    public static function logout() {
        self::start();
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_name']);
        unset($_SESSION['admin_role']);
        unset($_SESSION['admin_logged_in']);
        session_destroy();
    }
    
    public static function isLoggedIn() {
        self::start();
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }
    
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: admin_login.php');
            exit();
        }
    }
    
    public static function getAdminId() {
        self::start();
        return $_SESSION['admin_id'] ?? null;
    }
    
    public static function getAdminName() {
        self::start();
        return $_SESSION['admin_name'] ?? null;
    }
    
    public static function getAdminRole() {
        self::start();
        return $_SESSION['admin_role'] ?? null;
    }
}
?>
