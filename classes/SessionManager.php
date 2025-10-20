<?php
class SessionManager {
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public static function requireLogin($redirect_to = 'login.php') {
        if (!self::isLoggedIn()) {
            header("Location: $redirect_to");
            exit;
        }
    }
    
    public static function setUser($user_id, $user_name, $role = 'user') {
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = $user_name;
        $_SESSION['user_role'] = $role;
    }
    
    public static function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    public static function getUserName() {
        return $_SESSION['user_name'] ?? null;
    }
    
    public static function getUserRole() {
        return $_SESSION['user_role'] ?? 'user';
    }
    
    public static function isAdmin() {
        return self::getUserRole() === 'admin';
    }
    
    public static function logout() {
        session_unset();
        session_destroy();
    }
    
    public static function setFlashMessage($type, $message) {
        $_SESSION['flash'][$type] = $message;
    }
    
    public static function getFlashMessage($type) {
        $message = $_SESSION['flash'][$type] ?? null;
        unset($_SESSION['flash'][$type]);
        return $message;
    }
    
    public static function hasFlashMessage($type) {
        return isset($_SESSION['flash'][$type]);
    }
}
?>
