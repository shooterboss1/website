<?php
require_once 'config.php';

echo "<h2>Database Repair Tool</h2>";

// 1. Check if 'orders' table exists
$check = $conn->query("SHOW TABLES LIKE 'orders'");
if($check->num_rows == 0) {
    echo "Orders table missing. Creating it...<br>";
    // Table creation code handled by process_order.php essentially, but let's do it here
    $sql = "CREATE TABLE orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        paypal_order_id VARCHAR(100) UNIQUE,
        customer_email VARCHAR(100),
        customer_name VARCHAR(100),
        total_amount DECIMAL(10, 2),
        order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status VARCHAR(20) DEFAULT 'completed'
    )";
    if($conn->query($sql)) echo "✅ Created 'orders' table.<br>";
    else echo "❌ Failed to create table: " . $conn->error . "<br>";
} else {
    echo "Orders table exists. Checking columns...<br>";
    
    // Get columns
    $columns = [];
    $res = $conn->query("SHOW COLUMNS FROM orders");
    while($row = $res->fetch_assoc()) {
        $columns[] = $row['Field'];
    }
    
    // Fix: total_price -> total_amount
    if(in_array('total_price', $columns) && !in_array('total_amount', $columns)) {
        echo "Found old column 'total_price'. Renaming to 'total_amount'...<br>";
        $conn->query("ALTER TABLE orders CHANGE total_price total_amount DECIMAL(10, 2)");
    } elseif(!in_array('total_amount', $columns)) {
        echo "Adding missing column 'total_amount'...<br>";
        $conn->query("ALTER TABLE orders ADD COLUMN total_amount DECIMAL(10, 2) AFTER id");
    } else {
        echo "✅ 'total_amount' column exists.<br>";
    }
    
    // Fix other missing columns from old schema
    if(!in_array('customer_name', $columns)) {
        echo "Adding 'customer_name'...<br>";
        $conn->query("ALTER TABLE orders ADD COLUMN customer_name VARCHAR(100)");
    }
    
    if(!in_array('customer_email', $columns)) {
         echo "Adding 'customer_email'...<br>";
        $conn->query("ALTER TABLE orders ADD COLUMN customer_email VARCHAR(100)");
    }
    
    if(!in_array('status', $columns)) {
         echo "Adding 'status'...<br>";
        $conn->query("ALTER TABLE orders ADD COLUMN status VARCHAR(20) DEFAULT 'pending'");
    }
    
    if(!in_array('paypal_order_id', $columns)) {
         echo "Adding 'paypal_order_id'...<br>";
        $conn->query("ALTER TABLE orders ADD COLUMN paypal_order_id VARCHAR(100)");
    }
}

echo "<br><strong>Database repair complete!</strong> <a href='admin.php'>Go back to Admin</a>";

$conn->close();
?>
