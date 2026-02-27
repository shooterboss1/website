<?php
require_once 'config.php';

$name = 'Classic Fit Blazer';
$price = 69.99;
$category = 'women';
$image = 'images/blazer suit.jpeg';
$rating = 4.8;
$condition = 'new';
$seller = 'H&M';

$stmt = $conn->prepare("INSERT INTO products (name, price, category, image, rating, product_condition, seller_name) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sdssdss", $name, $price, $category, $image, $rating, $condition, $seller);

if ($stmt->execute()) {
    echo "Blazer added successfully!";
} else {
    echo "Error adding blazer: " . $stmt->error;
}

$conn->close();
?>
