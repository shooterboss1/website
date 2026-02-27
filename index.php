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

// HERO IMAGES
$heroSmall = image_or_placeholder([
    'images/herolarge.jpg',
    'images/hero-small.jpg',
    'images/herosmall.jpg',
], 'Hero');

$heroLarge = image_or_placeholder([
    'images/herolarge.jpg',
], 'Hero');

$logo = image_or_placeholder([
    'images/logo.jpeg?v=1.2',
    'images/logo.png',
], 'Logo');

// Hero/Logo definitions kept static for now
// PRODUCTS - Removed hardcoded arrays as we now fetch from DB

// added: debug comment and optional visible debug panel
// usage of product vars in debug removed
$resolved_debug = sprintf(
	'heroSmall=%s heroLarge=%s logo=%s',
	$heroSmall, $heroLarge, $logo
);
echo "<!-- resolved images: " . htmlspecialchars($resolved_debug, ENT_QUOTES) . " -->";

// show visible debug panel when ?debug=1
if (!empty($_GET['debug'])) {
	?>
	<style>.debug-panel{background:#111;color:#fff;padding:10px;font-family:monospace;font-size:13px}</style>
	<div class="debug-panel" role="note" aria-live="polite">
		Resolved images: <?php echo htmlspecialchars($resolved_debug, ENT_QUOTES); ?>
	</div>
	<?php
}

// helper to detect data-uri placeholders
function is_data_uri($src) {
	return strpos($src, 'data:image/svg+xml') === 0;
}
?>

<!-- HERO -->
<section class="hero">
    <img src="images/herolarge.jpg" alt="Hero image">
    <div class="hero-content">
        <img src="images/logo.jpeg?v=1.2" alt="NEXTGEN FDM" class="hero-logo">
        <h1 class="hero-title">NEXTGEN FDM</h1>
        <a href="shop_now.php" class="hero-btn">Shop Now</a>
    </div>
</section>

<!-- VIDEO CAMPAIGN AD -->
<section class="video-campaign">
    <!-- Placeholder video source. Replace 'videos/campaign.mp4' with your actual video file. -->
    <video autoplay muted loop playsinline poster="images/herolarge.jpg">
        <source src="videos/campaign.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>
    <div class="campaign-content">
        <h2>The Future is Here</h2>
        <p>Watch the campaign film.</p>
        <button class="mute-btn" onclick="document.querySelector('video').muted = !document.querySelector('video').muted; this.innerHTML = document.querySelector('video').muted ? '<i class=\'fa fa-volume-mute\'></i> Unmute' : '<i class=\'fa fa-volume-up\'></i> Mute';">
            <i class="fa fa-volume-mute"></i> Unmute
        </button>
    </div>
</section>

<!-- MAIN CONTENT -->
<div class="container">
	<aside class="sidebar">
		<h4>Shop by Product</h4>
		<ul class="filter-cats">
			<li><button type="button" class="cat-filter" data-cat="">View All</button></li>
			<li><button type="button" class="cat-filter" data-cat="men">Men</button></li>
			<li><button type="button" class="cat-filter" data-cat="women">Women</button></li>
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
        <input type="hidden" id="search-category" value="">
		<div class="controls">
			<div class="results-count"><span id="results-count">0</span> items</div>
			<div class="sort">
				Sort by: 
				<select id="sort-by" style="border:none; background:transparent; font-family:inherit; cursor:pointer;">
					<option value="relevance">Recommended</option>
					<option value="price-asc">Lowest Price</option>
					<option value="price-desc">Highest Price</option>
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
					<div class="product-price">$<?php echo $price; ?></div>
				</div>
			</li>
            <?php
                }
            } else {
                echo "<p>No products found.</p>";
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
</div>

<!-- CHANGED: JS - filtering, sorting, view toggle, integrate with existing cart code -->

<!-- Modals -->


<?php include 'includes/footer.php'; ?>
