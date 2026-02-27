<?php
require_once 'config.php';

// Create Products Table
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
    echo "Table 'products' created successfully (or already exists).<br>";
} else {
    echo "Error creating table 'products': " . $conn->error . "<br>";
}

// Create Sales Data Table (for Chart)
$sql_sales = "CREATE TABLE IF NOT EXISTS sales_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    month VARCHAR(20),
    sales_count INT,
    revenue DECIMAL(10, 2),
    year INT DEFAULT 2024
)";

if ($conn->query($sql_sales) === TRUE) {
    echo "Table 'sales_data' created successfully (or already exists).<br>";
} else {
    echo "Error creating table 'sales_data': " . $conn->error . "<br>";
}

// Create Orders Table (for Buy Now)
$sql_orders = "CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    quantity INT DEFAULT 1,
    total_price DECIMAL(10, 2),
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id)
)";

if ($conn->query($sql_orders) === TRUE) {
    echo "Table 'orders' created successfully (or already exists).<br>";
} else {
    echo "Error creating table 'orders': " . $conn->error . "<br>";
}

// Create Users Table
$sql_users = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql_users) === TRUE) {
    echo "Table 'users' created successfully (or already exists).<br>";
} else {
    echo "Error creating table 'users': " . $conn->error . "<br>";
}

// Insert Sample Products if table is empty
$result = $conn->query("SELECT COUNT(*) as count FROM products");
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    $stmt = $conn->prepare("INSERT INTO products (name, price, category, image, rating, product_condition, seller_name) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    $products = [
        ['Signature Hoodie', 49.99, 'apparel', 'images/hoodie.png', 4.6, 'new', 'RealThreads'],
        ['Signature T-Shirt', 29.99, 'apparel', 'images/tshirt.jpg', 4.2, 'new', 'UrbanWear'],
        ['Branded Cap', 19.99, 'headwear', 'images/cap.jpg', 4.0, 'new', 'HatMasters']
    ];

    foreach ($products as $p) {
        $stmt->bind_param("sdssdss", $p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6]);
        $stmt->execute();
    }
    echo "Sample products inserted.<br>";
    $stmt->close();
}

// Insert Sample Sales Data if table is empty
$result = $conn->query("SELECT COUNT(*) as count FROM sales_data");
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    $sql_insert_sales = "INSERT INTO sales_data (month, sales_count, revenue) VALUES 
        ('Jan', 10, 500.00),
        ('Feb', 15, 750.00),
        ('Mar', 20, 1000.00),
        ('Apr', 25, 1250.00),
        ('May', 30, 1500.00),
        ('Jun', 35, 1750.00)";
    
    if ($conn->query($sql_insert_sales) === TRUE) {
        echo "Sample sales data inserted.<br>";
    } else {
         echo "Error inserting sales data: " . $conn->error . "<br>";
    }
}

$conn->close();
?>
