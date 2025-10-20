<?php
require_once 'classes/SessionManager.php';

SessionManager::start();

echo "<h2>Role System Test</h2>";

if (SessionManager::isLoggedIn()) {
    echo "<p><strong>Logged in as:</strong> " . SessionManager::getUserName() . "</p>";
    echo "<p><strong>Role:</strong> " . SessionManager::getUserRole() . "</p>";
    echo "<p><strong>Is Admin:</strong> " . (SessionManager::isAdmin() ? 'Yes' : 'No') . "</p>";
    
    if (SessionManager::isAdmin()) {
        echo "<p style='color: green;'>✅ You have admin access!</p>";
        echo "<p><a href='admin_dashboard.php'>Go to Admin Dashboard</a></p>";
    } else {
        echo "<p style='color: blue;'>ℹ️ You are a regular user.</p>";
        echo "<p><a href='dashboard.php'>Go to User Dashboard</a></p>";
    }
} else {
    echo "<p style='color: red;'>❌ Not logged in.</p>";
    echo "<p><a href='login.php'>Login</a> | <a href='admin_login.php'>Admin Login</a></p>";
}
?>
