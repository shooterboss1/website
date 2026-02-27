<?php include 'includes/header.php'; ?>

<?php
// Enable debugging (disable in production)
ini_set('display_errors', '1');
error_reporting(E_ALL);
require_once 'config.php';

function image_or_placeholder(array $paths, $alt = '') {
	foreach ($paths as $p) {
		if (file_exists(__DIR__ . '/' . $p)) return $p;
	}
	$escapedAlt = htmlspecialchars($alt, ENT_QUOTES);
	$svgContent = '<svg xmlns="http://www.w3.org/2000/svg" width="600" height="300">'
		. '<rect width="100%" height="100%" fill="#ddd"/>'
		. '<text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#666" font-family="Arial" font-size="20">'
		. $escapedAlt
		. '</text></svg>';
	return 'data:image/svg+xml;charset=utf-8,' . rawurlencode($svgContent);
}

// HERO IMAGE
$heroImage = image_or_placeholder([
    'images/men-hero.jpg',
    'images/herolarge.jpg',
], 'Men\'s Collection');
?>



<!-- Breadcrumb -->
<div class="breadcrumb">
    <a href="index.php">Home</a> / <span>Men</span>
</div>

<!-- HERO -->
<section class="category-hero">
    <img src="<?php echo $heroImage; ?>" alt="Men's Collection">
    <div class="category-hero-content">
        <h1>Men's Collection</h1>
        <p>Elevate Your Style</p>
    </div>
</section>

<!-- Category Stats -->
<?php
// Get statistics
$totalResult = $conn->query("SELECT COUNT(*) as total FROM products WHERE category = 'men'");
$totalRow = $totalResult->fetch_assoc();
$totalProducts = $totalRow['total'];

$newResult = $conn->query("SELECT COUNT(*) as total FROM products WHERE category = 'men' AND product_condition = 'new'");
$newRow = $newResult->fetch_assoc();
$newProducts = $newRow['total'];

$saleResult = $conn->query("SELECT COUNT(*) as total FROM products WHERE category = 'men' AND product_condition = 'sale'");
$saleRow = $saleResult->fetch_assoc();
$saleProducts = $saleRow['total'];
?>

<div class="category-stats">
    <div class="stat-item">
        <span class="stat-number"><?php echo $totalProducts; ?></span>
        <span class="stat-label">Total Products</span>
    </div>
    <div class="stat-item">
        <span class="stat-number"><?php echo $newProducts; ?></span>
        <span class="stat-label">New Arrivals</span>
    </div>
    <div class="stat-item">
        <span class="stat-number"><?php echo $saleProducts; ?></span>
        <span class="stat-label">On Sale</span>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="container">
	<aside class="sidebar">
		<h4>Filter by Type</h4>
		<ul class="filter-cats">
			<li><button type="button" class="cat-filter" data-cat="">All Men's</button></li>
			<li><button type="button" class="cat-filter" data-cat="apparel">Clothing</button></li>
			<li><button type="button" class="cat-filter" data-cat="headwear">Accessories</button></li>
            <li><button type="button" class="cat-filter" data-cat="sport">Sportswear</button></li>
		</ul>

		<h4>Filter</h4>
		<ul>
			<li><label><input type="checkbox" class="cond-filter" value="new"> New Arrivals</label></li>
			<li><label><input type="checkbox" class="cond-filter" value="sale"> On Sale</label></li>
		</ul>
	</aside>

	<main class="main-content">
        <input type="hidden" id="search-category" value="men">
		<div class="controls">
			<div class="results-count"><span id="results-count">0</span> items</div>
			<div class="sort">
				Sort by: 
				<select id="sort-by" style="border:none; background:transparent; font-family:inherit; cursor:pointer;">
					<option value="relevance">Recommended</option>
					<option value="price-asc">Lowest Price</option>
					<option value="price-desc">Highest Price</option>
                    <option value="newest">Newest First</option>
				</select>
			</div>
		</div>

		<ul class="products grid" id="product-list">
            <?php
            $sql = "SELECT * FROM products WHERE category = 'men' ORDER BY id DESC LIMIT 12";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $id = $row['id'];
                    $name = htmlspecialchars($row['name']);
                    $price = $row['price'];
                    $cat = htmlspecialchars($row['category']);
                    $img = htmlspecialchars($row['image']);
                    
                    $rating = $row['rating'];
                    $cond = htmlspecialchars($row['product_condition']);
                    
                    // Fallback image
                    if (!file_exists(__DIR__ . '/' . $img)) {
                        $img = 'https://via.placeholder.com/400x600?text=' . urlencode($name);
                    }
            ?>
			<li class="card" data-id="<?php echo $id; ?>" data-cat="<?php echo $cat; ?>" data-price="<?php echo $price; ?>" data-title="<?php echo $name; ?>" data-cond="<?php echo $cond; ?>" data-rating="<?php echo $rating; ?>" data-img="<?php echo $img; ?>">
				<div style="position:relative; overflow:hidden;">
                    <img src="<?php echo $img; ?>" loading="lazy" class="product-image" alt="<?php echo $name; ?>">
                    <button type="button" class="fav-toggle"><i class="fa fa-heart"></i></button>
                    <button type="button" class="quick-add-btn">Add to Bag</button>
                </div>
				<div class="product-info">
					<h3 class="product-title"><?php echo $name; ?></h3>
					<div class="product-price">$<?php echo $price; ?></div>
				</div>
                <?php if ($cond === 'new'): ?>
                    <div style="font-size: 11px; color: #28a745; font-weight: 600; margin-top: 5px;">NEW ARRIVAL</div>
                <?php elseif ($cond === 'sale'): ?>
                    <div style="font-size: 11px; color: #dc3545; font-weight: 600; margin-top: 5px;">ON SALE</div>
                <?php endif; ?>
			</li>
            <?php
                }
            } else {
                echo '<div style="text-align:center; padding:60px 20px; grid-column:1/-1;">
                        <h2 style="margin-bottom:15px;">No Men\'s Products Yet</h2>
                        <p style="color:#666;">Check back soon for our men\'s collection.</p>
                      </div>';
            }
            ?>
		</ul>
        
        <!-- Load More Button -->
        <div style="text-align:center; margin-top:60px; margin-bottom:40px;">
            <button id="load-more-btn" style="padding:16px 50px; border:2px solid #000; background:#fff; color:#000; text-transform:uppercase; font-size:13px; font-weight:700; cursor:pointer; letter-spacing:1.5px; transition:all 0.3s ease; border-radius:2px; position:relative; overflow:hidden;">
                <span style="position:relative; z-index:1;">Load More Products</span>
            </button>
        </div>
	</main>
</div>

<!-- Modals -->


<?php include 'includes/footer.php'; ?>
