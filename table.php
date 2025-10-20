<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "racenex_db";

// Create connection
$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully.<br>";
} else {
    echo "Error creating database: " . $conn->error;
}

$conn->select_db($dbname);

// Create tables
$sqls = [];

// Users table
$sqls[] = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
)";

// Products table
$sqls[] = "CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

// Cart table
$sqls[] = "CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
)";

// Orders table
$sqls[] = "CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    address TEXT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50),
    payment_id VARCHAR(255),
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
)";

// Order Items table
$sqls[] = "CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
)";

// Add created_at column to existing products table if it doesn't exist
$sqls[] = "ALTER TABLE products ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";

// Add missing columns to orders table if they don't exist
$sqls[] = "ALTER TABLE orders ADD COLUMN IF NOT EXISTS payment_id VARCHAR(255)";
$sqls[] = "ALTER TABLE orders ADD COLUMN IF NOT EXISTS status VARCHAR(50) DEFAULT 'pending'";

// Update foreign key constraints to add CASCADE DELETE
$sqls[] = "ALTER TABLE order_items DROP FOREIGN KEY IF EXISTS order_items_ibfk_1";
$sqls[] = "ALTER TABLE order_items DROP FOREIGN KEY IF EXISTS order_items_ibfk_2";
$sqls[] = "ALTER TABLE order_items ADD CONSTRAINT order_items_ibfk_1 FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE";
$sqls[] = "ALTER TABLE order_items ADD CONSTRAINT order_items_ibfk_2 FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE";

// Sample data for products (insert only if not exists by checking slug)
$sqls[] = "INSERT INTO products (name, slug, description, price, image, stock) 
SELECT * FROM (SELECT 'High Performance Brake Pads' as name, 'brake-pads' as slug, 'Premium brake pads for high-speed performance.' as description, 999.99 as price, 'images/brakepads.jpg' as image, 20 as stock) AS tmp
WHERE NOT EXISTS (SELECT slug FROM products WHERE slug = 'brake-pads') LIMIT 1";

$sqls[] = "INSERT INTO products (name, slug, description, price, image, stock) 
SELECT * FROM (SELECT 'Sport Air Filter' as name, 'sport-air-filter' as slug, 'High-flow air filter for increased engine breathing.' as description, 799.99 as price, 'images/airfilter.jpg' as image, 35 as stock) AS tmp
WHERE NOT EXISTS (SELECT slug FROM products WHERE slug = 'sport-air-filter') LIMIT 1";

$sqls[] = "INSERT INTO products (name, slug, description, price, image, stock) 
SELECT * FROM (SELECT 'Racing Spark Plug' as name, 'racing-spark-plug' as slug, 'High-ignition spark plug for performance tuning.' as description, 949.99 as price, 'images/sparkplug.jpg' as image, 100 as stock) AS tmp
WHERE NOT EXISTS (SELECT slug FROM products WHERE slug = 'racing-spark-plug') LIMIT 1";

$sqls[] = "INSERT INTO products (name, slug, description, price, image, stock) 
SELECT * FROM (SELECT 'Alloy Racing Wheel - 17\"' as name, 'racing-wheel-17' as slug, 'Lightweight alloy wheel for racing applications.' as description, 25,000.99 as price, 'images/wheel.jpg' as image, 10 as stock) AS tmp
WHERE NOT EXISTS (SELECT slug FROM products WHERE slug = 'racing-wheel-17') LIMIT 1";

foreach ($sqls as $query) {
    if ($conn->query($query) === TRUE) {
        echo "Query executed successfully.<br>";
    } else {
        echo "Error: " . $conn->error . "<br>";
    }
}

echo "<h3>✅ Database and tables created successfully for RaceNex!</h3>";

$conn->close();
?>
