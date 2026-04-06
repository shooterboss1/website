<?php
header('Content-Type: application/json');
require_once 'config.php';

// Get POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'Invalid data']);
    exit;
}

$order_id = $data['order_id'] ?? '';
$payer_email = $data['payer']['email_address'] ?? 'guest';
$payer_name = ($data['payer']['name']['given_name'] ?? '') . ' ' . ($data['payer']['name']['surname'] ?? '');
$cart = $data['cart'] ?? [];

// Create orders table if it doesn't exist (extended version)
$sql_create_orders = "CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paypal_order_id VARCHAR(100) UNIQUE,
    customer_email VARCHAR(100),
    customer_name VARCHAR(100),
    total_amount DECIMAL(10, 2),
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) DEFAULT 'completed'
)";

$conn->query($sql_create_orders);

// Create order_items table if it doesn't exist
$sql_create_order_items = "CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    product_name VARCHAR(255),
    quantity INT,
    price DECIMAL(10, 2),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
)";

$conn->query($sql_create_order_items);

// SECURE PRICE CALCULATION
$subtotal = 0;
$secure_cart = [];

$stmt_price = $conn->prepare("SELECT name, price FROM products WHERE id = ?");

foreach ($cart as $item) {
    if (!isset($item['id']) || !isset($item['qty'])) continue;
    
    $product_id = (int)$item['id'];
    $quantity = (int)$item['qty'];
    
    if ($quantity <= 0) continue;
    
    $stmt_price->bind_param("i", $product_id);
    $stmt_price->execute();
    $result = $stmt_price->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $true_price = (float)$row['price'];
        $true_name = $row['name'];
        
        $subtotal += ($true_price * $quantity);
        
        // Save secure item details for insertion later
        $secure_cart[] = [
            'id' => $product_id,
            'name' => $true_name,
            'qty' => $quantity,
            'price' => $true_price
        ];
    }
}
$stmt_price->close();

if (empty($secure_cart)) {
    echo json_encode(['success' => false, 'error' => 'Cart is empty or contains invalid items.']);
    exit;
}

// Calculate Shipping and Tax exactly as the frontend does:
// const shipping = subtotal > 100 ? 0 : 10;
// const tax = subtotal * 0.08;
$shipping = $subtotal > 100 ? 0 : 10;
$tax = $subtotal * 0.08;
$secure_total_amount = $subtotal + $shipping + $tax;

// Insert order with SECURE total amount
$stmt = $conn->prepare("INSERT INTO orders (paypal_order_id, customer_email, customer_name, total_amount) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sssd", $order_id, $payer_email, $payer_name, $secure_total_amount);

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'error' => 'Failed to save order: ' . $stmt->error]);
    exit;
}

$inserted_order_id = $conn->insert_id;

// Insert order items using SECURE cart data
$stmt_items = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, price) VALUES (?, ?, ?, ?, ?)");

foreach ($secure_cart as $item) {
    $stmt_items->bind_param("iisid", $inserted_order_id, $item['id'], $item['name'], $item['qty'], $item['price']);
    $stmt_items->execute();
}

$stmt->close();
$stmt_items->close();

// --- SEND EMAIL NOTIFICATIONS USING EMAIL SERVICE ---
try {
    $emailService = new EmailService();
    
    // Prepare items array for email
    $emailItems = [];
    foreach ($secure_cart as $item) {
        $emailItems[] = [
            'product_name' => $item['name'],
            'quantity' => $item['qty'],
            'price' => $item['price']
        ];
    }
    
    // Send order confirmation to customer
    $emailService->sendOrderConfirmation(
        $payer_email,
        $payer_name,
        $inserted_order_id,
        $emailItems,
        $secure_total_amount
    );
    
    // Send notification to admin
    $emailService->sendAdminNotification(
        $inserted_order_id,
        $payer_email,
        $payer_name,
        $secure_total_amount
    );
} catch (Exception $e) {
    // Log error but don't break the JSON response
    logSecurityEvent('email_service_error', ['error' => $e->getMessage()]);
}

$conn->close();

// Return success
echo json_encode([
    'success' => true,
    'order_id' => $inserted_order_id,
    'paypal_order_id' => $order_id
]);
?>
