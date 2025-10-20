<?php
// db.php - OOP Database Configuration
require_once 'classes/Database.php';

// Create global database instance
$db = new Database();
$mysqli = $db->getConnection();
?>
