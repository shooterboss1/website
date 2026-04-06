<?php
/**
 * Database Migration
 * Adds missing tables and columns for complete e-commerce functionality
 */

require_once 'config.php';

// Check if user is admin
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. Admin only.");
}

echo "<pre>";
echo "=== Database Migration ===\n\n";

$migrations = [
    // Add inventory tracking to products
    "ALTER TABLE products ADD COLUMN stock_quantity INT DEFAULT 999 AFTER price" => "Add stock quantity to products",
    "ALTER TABLE products ADD COLUMN low_stock_threshold INT DEFAULT 10 AFTER stock_quantity" => "Add low stock alert threshold",
    
    // Enhance orders table
    "ALTER TABLE orders ADD COLUMN status VARCHAR(50) DEFAULT 'pending' AFTER order_date" => "Add order status field",
    "ALTER TABLE orders ADD COLUMN shipping_address TEXT AFTER status" => "Add shipping address",
    "ALTER TABLE orders ADD COLUMN tracking_number VARCHAR(100) AFTER shipping_address" => "Add tracking number",
    "ALTER TABLE orders ADD COLUMN payment_method VARCHAR(50) DEFAULT 'paypal' AFTER tracking_number" => "Add payment method",
    "ALTER TABLE orders ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at" => "Add updated timestamp",
    
    // Create customer accounts table
    "CREATE TABLE IF NOT EXISTS customer_accounts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(100) UNIQUE,
        password_hash VARCHAR(255),
        first_name VARCHAR(100),
        last_name VARCHAR(100),
        phone VARCHAR(20),
        default_address_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY email_unique (email)
    )" => "Create customer accounts table",
    
    // Create customer addresses table
    "CREATE TABLE IF NOT EXISTS customer_addresses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT NOT NULL,
        address_line1 VARCHAR(255),
        address_line2 VARCHAR(255),
        city VARCHAR(100),
        state VARCHAR(100),
        postal_code VARCHAR(20),
        country VARCHAR(100),
        is_default BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (customer_id) REFERENCES customer_accounts(id) ON DELETE CASCADE
    )" => "Create customer addresses table",
    
    // Create password reset tokens table
    "CREATE TABLE IF NOT EXISTS password_reset_tokens (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        token VARCHAR(255) UNIQUE,
        expires_at DATETIME,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )" => "Create password reset tokens table",
    
    // Create inventory log for tracking changes
    "CREATE TABLE IF NOT EXISTS inventory_log (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        quantity_changed INT,
        reason VARCHAR(255),
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )" => "Create inventory log table",
];

$successful = 0;
$failed = 0;
$skipped = 0;

foreach ($migrations as $sql => $description) {
    echo "Running: $description... ";
    
    try {
        $conn->query($sql);
        
        if ($conn->error) {
            // Some errors are expected (column already exists, etc.)
            if (strpos($conn->error, 'Duplicate column') !== false ||
                strpos($conn->error, 'already exists') !== false) {
                echo "SKIPPED (already exists)\n";
                $skipped++;
            } else {
                echo "FAILED\n";
                echo "  Error: " . $conn->error . "\n";
                $failed++;
            }
        } else {
            echo "OK\n";
            $successful++;
        }
    } catch (Exception $e) {
        echo "FAILED\n";
        echo "  Exception: " . $e->getMessage() . "\n";
        $failed++;
    }
}

echo "\n=== Migration Complete ===\n";
echo "Successful: $successful\n";
echo "Skipped: $skipped\n";
echo "Failed: $failed\n";

echo "\n<a href='admin.php'>Return to Admin</a>";
echo "</pre>";
?>
