<?php
$servername = "localhost";
$username = "root";
$password = "";

// 1. Connect without DB
$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. Create DB
$sql = "CREATE DATABASE IF NOT EXISTS clothing";
if ($conn->query($sql) === TRUE) {
    echo "Database 'clothing' checked/created successfully.\n";
} else {
    die("Error creating database: " . $conn->error);
}

// 3. Select DB
$conn->select_db("clothing");

// 4. Create Tables
$sql_products = "CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    category VARCHAR(50),
    image VARCHAR(255),
    rating DECIMAL(3, 1) DEFAULT 0.0,
    product_condition VARCHAR(20) DEFAULT 'new',
    seller_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql_products) === TRUE) {
    echo "Table 'products' checked/created successfully.\n";
} else {
    echo "Error creating table 'products': " . $conn->error . "\n";
}

// 5. Insert Data if empty
$result = $conn->query("SELECT COUNT(*) as count FROM products");
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    $stmt = $conn->prepare("INSERT INTO products (name, price, category, image, rating, product_condition, seller_name) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    $products = [
        ['Oversized Cotton T-shirt', 14.99, 'apparel', 'images/tshirt.jpg', 4.5, 'new', 'H&M'],
        ['Regular Fit Hoodie', 29.99, 'apparel', 'images/hoodie.png', 4.7, 'new', 'H&M'],
        ['Cotton Twill Cap', 9.99, 'headwear', 'images/cap.jpg', 4.2, 'new', 'H&M'],
        ['Slim Fit Jeans', 39.99, 'apparel', 'images/jeans.jpg', 4.4, 'new', 'H&M'],
        ['Sports Runner Jacket', 49.99, 'sport', 'images/jacket.jpg', 4.8, 'new', 'H&M Sport']
    ];

    foreach ($products as $p) {
        $stmt->bind_param("sdssdss", $p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6]);
        $stmt->execute();
    }
    echo "Sample products inserted.\n";
} else {
    echo "Products table already has data.\n";
}

$conn->close();
?>
