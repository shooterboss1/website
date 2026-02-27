<?php
/**
 * Helper Script: Add Products to Women's Collection
 * This script helps you add sample women's products to your database
 */

require_once 'config.php';

echo "<h1>Add Women's Products to Database</h1>";
echo "<style>body{font-family:Arial;padding:40px;} .success{color:green;} .error{color:red;} .info{color:blue;}</style>";

// Sample women's products to add
$womensProducts = [
    [
        'name' => 'Women\'s Classic Blazer',
        'price' => 89.99,
        'category' => 'women',
        'image' => 'images/women-blazer.jpg',
        'rating' => 4.5,
        'product_condition' => 'new'
    ],
    [
        'name' => 'Women\'s Signature T-Shirt',
        'price' => 29.99,
        'category' => 'women',
        'image' => 'images/women-tshirt.jpg',
        'rating' => 4.8,
        'product_condition' => 'new'
    ],
    [
        'name' => 'Women\'s Denim Jacket',
        'price' => 79.99,
        'category' => 'women',
        'image' => 'images/women-jacket.jpg',
        'rating' => 4.6,
        'product_condition' => 'sale'
    ],
    [
        'name' => 'Women\'s Summer Dress',
        'price' => 59.99,
        'category' => 'women',
        'image' => 'images/women-dress.jpg',
        'rating' => 4.7,
        'product_condition' => 'new'
    ],
    [
        'name' => 'Women\'s Yoga Pants',
        'price' => 45.99,
        'category' => 'women',
        'image' => 'images/women-yoga.jpg',
        'rating' => 4.9,
        'product_condition' => 'sale'
    ],
    [
        'name' => 'Women\'s Casual Cap',
        'price' => 24.99,
        'category' => 'women',
        'image' => 'images/women-cap.jpg',
        'rating' => 4.3,
        'product_condition' => 'new'
    ]
];

// Check if we should add the products
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'add') {
    echo "<h2>Adding Women's Products...</h2>";
    
    $added = 0;
    $errors = 0;
    
    foreach ($womensProducts as $product) {
        $stmt = $conn->prepare("INSERT INTO products (name, price, category, image, rating, product_condition) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sdssds", 
            $product['name'],
            $product['price'],
            $product['category'],
            $product['image'],
            $product['rating'],
            $product['product_condition']
        );
        
        if ($stmt->execute()) {
            echo "<p class='success'>✅ Added: {$product['name']} - \${$product['price']}</p>";
            $added++;
        } else {
            echo "<p class='error'>❌ Failed to add: {$product['name']}</p>";
            $errors++;
        }
    }
    
    echo "<hr>";
    echo "<h3 class='success'>Summary: Added $added products</h3>";
    if ($errors > 0) {
        echo "<h3 class='error'>Errors: $errors products failed</h3>";
    }
    echo "<p><a href='women.php'>View Women's Collection →</a></p>";
    echo "<p><a href='add_women_products.php'>← Back</a></p>";
    
} elseif ($action === 'convert') {
    // Convert existing products to women's category
    echo "<h2>Converting Existing Products to Women's Category...</h2>";
    echo "<p class='info'>This will change existing products to women's category based on your selection.</p>";
    
    // Get current products
    $result = $conn->query("SELECT id, name, category FROM products WHERE category != 'women' ORDER BY id DESC LIMIT 10");
    
    echo "<form method='POST' action='?action=convert_confirm'>";
    echo "<h3>Select products to convert to Women's category:</h3>";
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<label style='display:block; margin:10px 0;'>";
            echo "<input type='checkbox' name='products[]' value='{$row['id']}'> ";
            echo "<strong>{$row['name']}</strong> (Current: {$row['category']})";
            echo "</label>";
        }
        echo "<br><button type='submit' style='padding:10px 20px; background:#667eea; color:#fff; border:none; cursor:pointer; border-radius:4px;'>Convert Selected to Women's</button>";
    } else {
        echo "<p class='error'>No products available to convert.</p>";
    }
    echo "</form>";
    echo "<p><a href='add_women_products.php'>← Back</a></p>";
    
} elseif ($action === 'convert_confirm' && isset($_POST['products'])) {
    echo "<h2>Converting Products...</h2>";
    
    $converted = 0;
    foreach ($_POST['products'] as $productId) {
        $stmt = $conn->prepare("UPDATE products SET category = 'women' WHERE id = ?");
        $stmt->bind_param("i", $productId);
        
        if ($stmt->execute()) {
            $converted++;
            echo "<p class='success'>✅ Product ID $productId converted to women's category</p>";
        }
    }
    
    echo "<hr>";
    echo "<h3 class='success'>Converted $converted products to Women's category!</h3>";
    echo "<p><a href='women.php'>View Women's Collection →</a></p>";
    echo "<p><a href='add_women_products.php'>← Back</a></p>";
    
} else {
    // Show options
    ?>
    <h2>Choose an option:</h2>
    
    <div style="margin: 30px 0;">
        <h3>Option 1: Add Sample Women's Products</h3>
        <p>This will add 6 new women's products to your database.</p>
        <ul>
            <?php foreach ($womensProducts as $product): ?>
                <li><strong><?php echo $product['name']; ?></strong> - $<?php echo $product['price']; ?> 
                    <?php if ($product['product_condition'] === 'new'): ?>
                        <span style="color:#28a745;">(NEW)</span>
                    <?php elseif ($product['product_condition'] === 'sale'): ?>
                        <span style="color:#dc3545;">(SALE)</span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <a href="?action=add" style="display:inline-block; padding:12px 24px; background:#28a745; color:#fff; text-decoration:none; border-radius:4px; font-weight:bold;">
            Add These Products
        </a>
    </div>
    
    <hr style="margin: 40px 0;">
    
    <div style="margin: 30px 0;">
        <h3>Option 2: Convert Existing Products to Women's Category</h3>
        <p>Change existing products from other categories to women's category.</p>
        <a href="?action=convert" style="display:inline-block; padding:12px 24px; background:#667eea; color:#fff; text-decoration:none; border-radius:4px; font-weight:bold;">
            Convert Existing Products
        </a>
    </div>
    
    <hr style="margin: 40px 0;">
    
    <div style="margin: 30px 0;">
        <h3>Option 3: Manual Entry</h3>
        <p>Add your own custom women's product.</p>
        <form method="POST" action="?action=manual_add" style="max-width: 500px;">
            <div style="margin: 15px 0;">
                <label style="display:block; margin-bottom:5px; font-weight:bold;">Product Name:</label>
                <input type="text" name="name" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;">
            </div>
            
            <div style="margin: 15px 0;">
                <label style="display:block; margin-bottom:5px; font-weight:bold;">Price ($):</label>
                <input type="number" name="price" step="0.01" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;">
            </div>
            
            <div style="margin: 15px 0;">
                <label style="display:block; margin-bottom:5px; font-weight:bold;">Image Path:</label>
                <input type="text" name="image" value="images/women-product.jpg" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;">
                <small style="color:#666;">e.g., images/women-product.jpg</small>
            </div>
            
            <div style="margin: 15px 0;">
                <label style="display:block; margin-bottom:5px; font-weight:bold;">Rating (0-5):</label>
                <input type="number" name="rating" min="0" max="5" step="0.1" value="4.5" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;">
            </div>
            
            <div style="margin: 15px 0;">
                <label style="display:block; margin-bottom:5px; font-weight:bold;">Condition:</label>
                <select name="condition" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;">
                    <option value="new">New Arrival</option>
                    <option value="sale">On Sale</option>
                    <option value="regular">Regular</option>
                </select>
            </div>
            
            <button type="submit" style="padding:12px 24px; background:#f5576c; color:#fff; border:none; cursor:pointer; border-radius:4px; font-weight:bold;">
                Add Women's Product
            </button>
        </form>
    </div>
    
    <hr style="margin: 40px 0;">
    
    <div style="background:#f0f8ff; padding:20px; border-radius:8px; border-left:4px solid #0070ba;">
        <h3>Current Status:</h3>
        <?php
        $womenCount = $conn->query("SELECT COUNT(*) as count FROM products WHERE category = 'women'")->fetch_assoc();
        $totalCount = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc();
        
        echo "<p><strong>Women's Products:</strong> {$womenCount['count']}</p>";
        echo "<p><strong>Total Products:</strong> {$totalCount['count']}</p>";
        
        if ($womenCount['count'] == 0) {
            echo "<p class='error'><strong>⚠️ No women's products found!</strong> Use one of the options above to add some.</p>";
        } else {
            echo "<p class='success'><strong>✅ Women's collection has {$womenCount['count']} products</strong></p>";
            echo "<p><a href='women.php' style='color:#0070ba; font-weight:bold;'>View Women's Collection →</a></p>";
        }
        ?>
    </div>
    
    <?php
}

// Handle manual add
if ($action === 'manual_add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_POST['image'];
    $rating = $_POST['rating'];
    $condition = $_POST['condition'];
    
    $stmt = $conn->prepare("INSERT INTO products (name, price, category, image, rating, product_condition) VALUES (?, ?, 'women', ?, ?, ?)");
    $stmt->bind_param("sdsds", $name, $price, $image, $rating, $condition);
    
    if ($stmt->execute()) {
        echo "<h2 class='success'>✅ Successfully Added!</h2>";
        echo "<p><strong>$name</strong> has been added to the women's collection.</p>";
        echo "<p><a href='women.php'>View Women's Collection →</a></p>";
        echo "<p><a href='add_women_products.php'>← Add Another</a></p>";
    } else {
        echo "<h2 class='error'>❌ Error</h2>";
        echo "<p>Failed to add product: " . $conn->error . "</p>";
        echo "<p><a href='add_women_products.php'>← Try Again</a></p>";
    }
}

$conn->close();
?>
