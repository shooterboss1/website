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
    'images/shop-hero.jpg',
    'images/herolarge.jpg',
], 'Shop All Products');

// Get categories with product counts
$categoriesQuery = "SELECT category, COUNT(*) as count FROM products GROUP BY category";
$categoriesResult = $conn->query($categoriesQuery);
$categories = [];
while($catRow = $categoriesResult->fetch_assoc()) {
    $categories[$catRow['category']] = $catRow['count'];
}

// Get total product count
$totalQuery = "SELECT COUNT(*) as total FROM products";
$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalProducts = $totalRow['total'];
?>



<!-- Breadcrumb -->
<div class="breadcrumb">
    <a href="index.php">Home</a> / <span>Shop Now</span>
</div>

<!-- HERO -->
<section class="shop-hero">
    <img src="<?php echo $heroImage; ?>" alt="Shop All Products">
    <div class="shop-hero-content">
        <h1>Shop All</h1>
        <p>Discover Our Complete Collection - <?php echo $totalProducts; ?> Products Available</p>
    </div>
</section>

<!-- Category Quick Filter -->
<div class="category-grid">
    <div class="category-card active" data-category="">
        <h3>All</h3>
        <span class="count"><?php echo $totalProducts; ?> items</span>
    </div>
    <div class="category-card" data-category="men">
        <h3>Men</h3>
        <span class="count"><?php echo isset($categories['men']) ? $categories['men'] : 0; ?> items</span>
    </div>
    <div class="category-card" data-category="women">
        <h3>Women</h3>
        <span class="count"><?php echo isset($categories['women']) ? $categories['women'] : 0; ?> items</span>
    </div>
    <div class="category-card" data-category="apparel">
        <h3>Apparel</h3>
        <span class="count"><?php echo isset($categories['apparel']) ? $categories['apparel'] : 0; ?> items</span>
    </div>
    <div class="category-card" data-category="headwear">
        <h3>Accessories</h3>
        <span class="count"><?php echo isset($categories['headwear']) ? $categories['headwear'] : 0; ?> items</span>
    </div>
    <div class="category-card" data-category="dresses">
        <h3>Dresses</h3>
        <span class="count"><?php echo isset($categories['dresses']) ? $categories['dresses'] : 0; ?> items</span>
    </div>
</div>

<!-- Active Filters Display -->
<div class="filter-chips" id="active-filters" style="display: none;">
    <!-- Will be populated by JavaScript -->
</div>

<!-- MAIN CONTENT -->
<div class="container">
	<aside class="sidebar">
		<h4>Shop by Category</h4>
		<ul class="filter-cats">
			<li><button type="button" class="cat-filter" data-cat="">All Products</button></li>
			<li><button type="button" class="cat-filter" data-cat="men">Men</button></li>
			<li><button type="button" class="cat-filter" data-cat="women">Women</button></li>
			<li><button type="button" class="cat-filter" data-cat="apparel">Clothing</button></li>
            <li><button type="button" class="cat-filter" data-cat="dresses">Dresses</button></li>
			<li><button type="button" class="cat-filter" data-cat="headwear">Accessories</button></li>
            <li><button type="button" class="cat-filter" data-cat="sport">Sportswear</button></li>
		</ul>

		<h4>Product Condition</h4>
		<ul>
			<li><label><input type="checkbox" class="cond-filter" value="new"> New Arrivals</label></li>
			<li><label><input type="checkbox" class="cond-filter" value="sale"> On Sale</label></li>
		</ul>

        <h4>Price Range</h4>
        <ul>
            <li><label><input type="radio" name="price-range" class="price-filter" value="0-25"> Under $25</label></li>
            <li><label><input type="radio" name="price-range" class="price-filter" value="25-50"> $25 - $50</label></li>
            <li><label><input type="radio" name="price-range" class="price-filter" value="50-100"> $50 - $100</label></li>
            <li><label><input type="radio" name="price-range" class="price-filter" value="100-999"> Over $100</label></li>
        </ul>

        <h4>Rating</h4>
        <ul>
            <li><button type="button" class="rating-filter" data-min="4">⭐⭐⭐⭐ & Up</button></li>
            <li><button type="button" class="rating-filter" data-min="3">⭐⭐⭐ & Up</button></li>
        </ul>

        <div class="clear-filters-container">
            <button type="button" id="clear-all-filters">
                Clear All Filters
            </button>
        </div>
	</aside>

	<main class="main-content">
        <input type="hidden" id="search-category" value="">
		<div class="controls">
			<div class="results-count"><span id="results-count"><?php echo $totalProducts; ?></span> items</div>
            <div class="view-toggle">
                <button type="button" id="view-grid" class="active" title="Grid View">
                    <i class="fas fa-th"></i>
                </button>
                <button type="button" id="view-list" title="List View">
                    <i class="fas fa-list"></i>
                </button>
            </div>
			<div class="sort">
				Sort by: 
				<select id="sort-by" style="border:none; background:transparent; font-family:inherit; cursor:pointer;">
					<option value="relevance">Recommended</option>
					<option value="price-asc">Price: Low to High</option>
					<option value="price-desc">Price: High to Low</option>
                    <option value="newest">Newest First</option>
                    <option value="name-asc">Name: A-Z</option>
                    <option value="rating-desc">Highest Rated</option>
				</select>
			</div>
		</div>

		<ul class="products grid" id="product-list">
            <?php
            $sql = "SELECT * FROM products ORDER BY id DESC LIMIT 12";
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
                    
                    // Clean up image path for Windows/Linux compatibility
                    $img_path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $img);
                    
                    // Fallback image if file doesn't exist locally
                    if (!file_exists(__DIR__ . DIRECTORY_SEPARATOR . $img_path)) {
                         $img = 'images/logo.jpeg'; // Use brand logo as professional fallback
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
					<div class="product-price">$<?php echo number_format($price, 2); ?></div>
                    <?php if ($rating > 0): ?>
                        <div style="font-size: 11px; color: #f39c12; margin-top: 3px;">
                            <?php echo str_repeat('⭐', floor($rating)); ?> (<?php echo $rating; ?>)
                        </div>
                    <?php endif; ?>
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
                        <h2 style="margin-bottom:15px;">No Products Available</h2>
                        <p style="color:#666;">Please check back later.</p>
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

        <?php
        // Get total count for JS to know if there's more
        $total_res = $conn->query("SELECT COUNT(*) as total FROM products");
        $total_count = ($total_res) ? $total_res->fetch_assoc()['total'] : 0;
        ?>
        <span id="results-count" style="display:none;"><?php echo $total_count; ?></span>
        
	</main>
</div>

<!-- Cart Modal -->
<div id="cart-modal" class="modal-overlay">
    <div class="modal-content side-drawer">
        <div class="modal-header">
            <h3>Your Bag (<span id="cart-count-title">0</span>)</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div id="cart-items-container" class="modal-body">
            <!-- Items populated by JS -->
        </div>
        <div class="modal-footer">
            <div class="total-row"><span>Total:</span> <span id="cart-total-display">$0.00</span></div>
            <a href="checkout.php" class="btn-block">Checkout Now</a>
        </div>
    </div>
</div>

<!-- Favorites Modal -->
<div id="fav-modal" class="modal-overlay">
    <div class="modal-content side-drawer">
        <div class="modal-header">
            <h3>Favorites</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div id="fav-items-container" class="modal-body">
            <!-- Items populated by JS -->
        </div>
    </div>
</div>

<!-- Quick View Modal -->
<div id="quick-view-modal" class="modal-overlay">
    <div class="modal-content qv-modal">
        <button class="modal-close">&times;</button>
        <div class="qv-grid">
            <div class="qv-img-col">
                <img id="qv-image" src="" alt="Product">
            </div>
            <div class="qv-info-col">
                <div id="qv-condition" class="qv-badge"></div>
                <h1 id="qv-title"></h1>
                <div id="qv-price" class="qv-price"></div>
                <div id="qv-rating" class="qv-rating"></div>
                <p id="qv-description">Our premium quality apparel designed for style and comfort.</p>
                
                <div class="qv-actions">
                    <div class="qty-selector">
                        <button type="button" onclick="this.parentNode.querySelector('input').stepDown()">-</button>
                        <input type="number" id="qv-qty" value="1" min="1">
                        <button type="button" onclick="this.parentNode.querySelector('input').stepUp()">+</button>
                    </div>
                    <button id="qv-add-btn" class="btn-block">Add to Bag</button>
                </div>
                
                <div class="qv-meta">
                    Category: <span id="qv-category"></span>
                </div>
            </div>
        </div>
    </div>
</div>




<?php include 'includes/footer.php'; ?>
