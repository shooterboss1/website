<?php
// PayPal Integration Test Script
require_once 'config.php';

echo "<h1>PayPal Integration Status Check</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 40px; max-width: 800px; margin: 0 auto; }
    h1 { color: #0070ba; }
    .status { padding: 15px; margin: 10px 0; border-radius: 8px; }
    .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
    .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
    code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
    .check-item { margin: 20px 0; padding: 15px; background: #f9f9f9; border-left: 4px solid #0070ba; }
    h2 { color: #333; margin-top: 30px; }
</style>";

echo "<h2>✓ Integration Files</h2>";

// Check if files exist
$files = [
    'checkout.php' => 'PayPal Checkout Page',
    'process_order.php' => 'Order Processing Script',
    'PAYPAL_SETUP.md' => 'Setup Guide',
    'QUICK_START.md' => 'Quick Start Guide'
];

foreach ($files as $file => $description) {
    if (file_exists($file)) {
        echo "<div class='status success'>✓ <strong>$description</strong> - File exists: <code>$file</code></div>";
    } else {
        echo "<div class='status error'>✗ <strong>$description</strong> - File missing: <code>$file</code></div>";
    }
}

echo "<h2>✓ Database Tables</h2>";

// Check database tables
$tables = ['orders', 'order_items', 'products'];
foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result && $result->num_rows > 0) {
        // Count records
        $count_result = $conn->query("SELECT COUNT(*) as count FROM $table");
        $count = $count_result->fetch_assoc()['count'];
        echo "<div class='status success'>✓ Table '<code>$table</code>' exists - <strong>$count records</strong></div>";
    } else {
        echo "<div class='status error'>✗ Table '<code>$table</code>' is missing</div>";
    }
}

echo "<h2>✓ PayPal Configuration</h2>";

// Check if Client ID is configured
$checkout_content = file_get_contents('checkout.php');
if (strpos($checkout_content, 'YOUR_CLIENT_ID') !== false) {
    echo "<div class='status warning'>⚠ <strong>Action Required:</strong> PayPal Client ID not configured yet. Please replace <code>YOUR_CLIENT_ID</code> in checkout.php with your actual PayPal Client ID.</div>";
    echo "<div class='check-item'>";
    echo "<h3>How to get your Client ID:</h3>";
    echo "<ol>";
    echo "<li>Visit <a href='https://developer.paypal.com' target='_blank'>developer.paypal.com</a></li>";
    echo "<li>Sign in with your PayPal account (@nextgenfdm_payments)</li>";
    echo "<li>Go to 'My Apps & Credentials'</li>";
    echo "<li>Create a new app or use an existing one</li>";
    echo "<li>Copy the Client ID</li>";
    echo "<li>Open <code>checkout.php</code> and replace <code>YOUR_CLIENT_ID</code></li>";
    echo "</ol>";
    echo "</div>";
} else {
    echo "<div class='status success'>✓ PayPal Client ID appears to be configured (no placeholder found)</div>";
}

echo "<h2>✓ PayPal Account</h2>";
echo "<div class='status info'>💰 Payment Recipient: <strong>@charlesnagbe808</strong> (charlesnagbe808@paypal.com)</div>";

echo "<h2>✓ Sample Orders</h2>";

// Show recent orders
$orders_query = "SELECT * FROM orders ORDER BY order_date DESC LIMIT 5";
$orders_result = $conn->query($orders_query);

if ($orders_result && $orders_result->num_rows > 0) {
    echo "<table style='width:100%; border-collapse: collapse; margin-top: 20px;'>";
    echo "<tr style='background: #f4f4f4; text-align: left;'>";
    echo "<th style='padding: 10px; border: 1px solid #ddd;'>Order ID</th>";
    echo "<th style='padding: 10px; border: 1px solid #ddd;'>Customer</th>";
    echo "<th style='padding: 10px; border: 1px solid #ddd;'>Amount</th>";
    echo "<th style='padding: 10px; border: 1px solid #ddd;'>Date</th>";
    echo "</tr>";
    
    while ($order = $orders_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td style='padding: 10px; border: 1px solid #ddd; font-family: monospace; font-size: 12px;'>" . htmlspecialchars(substr($order['paypal_order_id'], 0, 20)) . "...</td>";
        echo "<td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($order['customer_name']) . "</td>";
        echo "<td style='padding: 10px; border: 1px solid #ddd;'>$" . number_format($order['total_amount'], 2) . "</td>";
        echo "<td style='padding: 10px; border: 1px solid #ddd;'>" . date('M d, Y H:i', strtotime($order['order_date'])) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<div class='status info'>No orders yet. Orders will appear here after the first purchase.</div>";
}

echo "<h2>✓ Products Available</h2>";

// Show products count by category
$products_query = "SELECT category, COUNT(*) as count FROM products GROUP BY category";
$products_result = $conn->query($products_query);

if ($products_result && $products_result->num_rows > 0) {
    echo "<ul>";
    while ($row = $products_result->fetch_assoc()) {
        echo "<li><strong>" . htmlspecialchars($row['category']) . ":</strong> " . $row['count'] . " products</li>";
    }
    echo "</ul>";
} else {
    echo "<div class='status warning'>⚠ No products found in database. Add products to start selling.</div>";
}

echo "<h2>✓ Next Steps</h2>";
echo "<div class='check-item'>";
echo "<ol>";
echo "<li><strong>Get PayPal Client ID</strong> from <a href='https://developer.paypal.com' target='_blank'>developer.paypal.com</a></li>";
echo "<li><strong>Update checkout.php</strong> with your Client ID</li>";
echo "<li><strong>Test in Sandbox mode</strong> with test accounts</li>";
echo "<li><strong>Switch to Live mode</strong> when ready</li>";
echo "<li><strong>Start selling!</strong> 🎉</li>";
echo "</ol>";
echo "</div>";

echo "<h2>✓ Quick Links</h2>";
echo "<ul>";
echo "<li><a href='index.php'>View Store</a></li>";
echo "<li><a href='checkout.php'>Test Checkout Page</a></li>";
echo "<li><a href='admin.php'>Admin Dashboard</a></li>";
echo "<li><a href='PAYPAL_SETUP.md' target='_blank'>Setup Guide</a></li>";
echo "<li><a href='QUICK_START.md' target='_blank'>Quick Start</a></li>";
echo "</ul>";

$conn->close();

echo "<hr style='margin: 40px 0;'>";
echo "<p style='text-align: center; color: #666;'>PayPal Integration Test Complete • NEXTGEN FDM Clothing Brand</p>";
?>
