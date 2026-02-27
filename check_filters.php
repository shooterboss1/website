<?php
require_once 'config.php';

echo "--- CATEGORIES ---\n";
$result = $conn->query("SELECT DISTINCT category FROM products");
while($row = $result->fetch_assoc()) {
    echo $row['category'] . "\n";
}

echo "\n--- CONDITIONS ---\n";
$result = $conn->query("SELECT DISTINCT product_condition FROM products");
while($row = $result->fetch_assoc()) {
    echo $row['product_condition'] . "\n";
}
?>
