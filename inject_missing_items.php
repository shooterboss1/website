<?php
require_once 'config.php';

echo "<h2>What's Real Shall Prosper Item Recovery</h2>";

$items = [
    [
        'name' => 'Structured Wool Coat',
        'price' => 129.99,
        'category' => 'men',
        'image' => 'images/structured_wool_coat.jpeg',
        'rating' => 5.0,
        'condition' => 'new'
    ],
    [
        'name' => 'Silk Blend Dress',
        'price' => 89.99,
        'category' => 'dresses',
        'image' => 'images/silk_blend_dress.jpeg',
        'rating' => 4.9,
        'condition' => 'new'
    ],
    [
        'name' => 'Velvet Evening Gown',
        'price' => 159.99,
        'category' => 'dresses',
        'image' => 'images/women.jpeg', 
        'rating' => 5.0,
        'condition' => 'featured'
    ]
];

foreach ($items as $item) {
    // Check if item already exists to avoid duplicates
    $check = $conn->prepare("SELECT id FROM products WHERE name = ?");
    $check->bind_param("s", $item['name']);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO products (name, price, category, image, rating, product_condition, seller_name) VALUES (?, ?, ?, ?, ?, ?, 'What's Real Shall Prosper')");
        $stmt->bind_param("sdssis", $item['name'], $item['price'], $item['category'], $item['image'], $item['rating'], $item['condition']);
        
        if ($stmt->execute()) {
            echo "✅ Added: <strong>" . $item['name'] . "</strong> to " . $item['category'] . "<br>";
        } else {
            echo "❌ Error adding " . $item['name'] . ": " . $stmt->error . "<br>";
        }
        $stmt->close();
    } else {
        echo "ℹ️ Item already exists: <strong>" . $item['name'] . "</strong> (Skipping duplicate)<br>";
        
        // Update image path just in case it was wrong
        $update = $conn->prepare("UPDATE products SET image = ? WHERE name = ?");
        $update->bind_param("ss", $item['image'], $item['name']);
        $update->execute();
        $update->close();
        echo "   -> Synced image path for " . $item['name'] . "<br>";
    }
}

echo "<br><strong>Done!</strong> These items are now live. <a href='index.php'>Go to Shop</a>";

$conn->close();
?>

