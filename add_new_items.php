<?php
// Example script to add Men or Women items
// Run this via command line: php add_new_items.php

require_once 'config.php';

// Example MEN's item
$name = 'Structured Wool Coat';
$price = 129.99;
$category = 'men'; // Use 'men' or 'women'
$image = 'images/men_coat.jpg'; // Ensure this image exists
$rating = 5.0;
$condition = 'new';
$seller = 'NEXTGEN FDM';

$stmt = $conn->prepare("INSERT INTO products (name, price, category, image, rating, product_condition, seller_name) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sdssdss", $name, $price, $category, $image, $rating, $condition, $seller);

if ($stmt->execute()) {
    echo "Product '$name' added to '$category' successfully!\n";
} else {
    echo "Error: " . $stmt->error . "\n";
}

// Example WOMEN item
$name2 = 'Silk Blend Dress';
$price = 89.99;
$category2 = 'women';
$image2 = 'images/ladies_dress.jpg';
$rating2 = 4.9;

$stmt2 = $conn->prepare("INSERT INTO products (name, price, category, image, rating, product_condition, seller_name) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt2->bind_param("sdssdss", $name2, $price, $category2, $image2, $rating2, $condition, $seller);

if ($stmt2->execute()) {
    echo "Product '$name2' added to '$category2' successfully!\n";
} else {
    echo "Error: " . $stmt->error . "\n";
}

$conn->close();
?>
