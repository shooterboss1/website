<?php
// Test script to verify process_order.php security fix
ini_set('display_errors', '1');
error_reporting(E_ALL);

echo "<h2>Testing Secure Order Processing</h2>";

// URL to process_order.php (assuming localhost environment matches codebase)
$url = 'http://localhost/ClothingBrandwebsite/process_order.php';

// Create a malicious payload where the price and total amount are artificially low
// Assuming there might be a product ID 1 (e.g., a jacket normally costing $100)
$malicious_payload = [
    'order_id' => 'TEST-' . time(),
    'payer' => [
        'email_address' => 'hacker@test.com',
        'name' => [
            'given_name' => 'Test',
            'surname' => 'Hacker'
        ]
    ],
    'cart' => [
        [
            'id' => 1,
            'title' => 'Expensive Item Modified to $1',
            'qty' => 1,
            'price' => 1.00 // Malicious price
        ]
    ],
    'amount' => 1.00 // Malicious total amount
];

echo "<p>Sending malicious payload (cart price $1.00, total amount $1.00) to process_order.php...</p>";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($malicious_payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p>HTTP Status: " . $http_code . "</p>";
echo "<p>Response: " . htmlspecialchars($response) . "</p>";

// Now verify the database
require_once 'config.php';

echo "<h3>Verifying Database Record</h3>";
$order_id_param = $malicious_payload['order_id'];

$stmt = $conn->prepare("SELECT id, total_amount FROM orders WHERE paypal_order_id = ?");
$stmt->bind_param("s", $order_id_param);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $inserted_id = $row['id'];
    $actual_total = (float)$row['total_amount'];
    
    echo "<p>Order inserted successfully (ID: " . $inserted_id . ").</p>";
    echo "<p><strong>Malicious amount sent:</strong> $1.00</p>";
    echo "<p><strong>Actual total amount saved:</strong> $" . number_format($actual_total, 2) . "</p>";
    
    if ($actual_total > 1.00) {
        echo "<p style='color:green;'><b>SUCCESS!</b> The backend ignored the malicious frontend price and used the correct database price.</p>";
    } else {
        echo "<p style='color:red;'><b>FAILURE!</b> The backend trusted the malicious frontend price.</p>";
    }
    
    // Check items
    $stmt_items = $conn->prepare("SELECT product_id, price FROM order_items WHERE order_id = ?");
    $stmt_items->bind_param("i", $inserted_id);
    $stmt_items->execute();
    $items_result = $stmt_items->get_result();
    
    echo "<h4>Order Items Saved:</h4><ul>";
    while ($item_row = $items_result->fetch_assoc()) {
        echo "<li>Product ID " . $item_row['product_id'] . " saved with price: $" . number_format($item_row['price'], 2) . "</li>";
    }
    echo "</ul>";
    
} else {
    echo "<p>Order was not found in the database. Product ID 1 might not exist or the script failed.</p>";
}
$stmt->close();
$conn->close();
?>
