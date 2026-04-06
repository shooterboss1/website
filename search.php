<?php include 'includes/header.php'; ?>

<?php
require_once 'config.php';

// Get search query and filters from URL
$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';
$category = isset($_GET['cat']) ? trim($_GET['cat']) : '';
$priceMin = isset($_GET['price_min']) ? (float)$_GET['price_min'] : 0;
$priceMax = isset($_GET['price_max']) ? (float)$_GET['price_max'] : 999999;
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'relevance';

// Build SQL query
$sql = "SELECT * FROM products WHERE 1=1";
$params = [];
$types = "";

// Search by name or category
if (!empty($searchQuery)) {
    $sql .= " AND (name LIKE ? OR category LIKE ? OR product_condition LIKE ?)";
    $searchTerm = "%{$searchQuery}%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "sss";
}

// Filter by category
if (!empty($category)) {
    $sql .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

// Filter by price range
$sql .= " AND price BETWEEN ? AND ?";
$params[] = $priceMin;
$params[] = $priceMax;
$types .= "dd";

// Sorting
switch($sortBy) {
    case 'price-asc':
        $sql .= " ORDER BY price ASC";
        break;
    case 'price-desc':
        $sql .= " ORDER BY price DESC";
        break;
    case 'name-asc':
        $sql .= " ORDER BY name ASC";
        break;
    case 'newest':
        $sql .= " ORDER BY id DESC";
        break;
    default:
        $sql .= " ORDER BY id DESC";
}

// Execute query
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$totalResults = $result->num_rows;
?>

<style>
.search-page {
    max-width: 1400px;
    margin: 80px auto 40px;
    padding: 0 20px;
}

.search-header {
    margin-bottom: 30px;
}

.search-header h1 {
    font-size: 2rem;
    margin-bottom: 10px;
}

.search-info {
    color: #666;
    font-size: 14px;
}

.search-info strong {
    color: #000;
}

.search-filters {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 30px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    align-items: end;
}

.filter-group {
    display: flex;
    flex-direction: column;
}

.filter-group label {
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 5px;
    color: #333;
}

.filter-group input,
.filter-group select {
    padding: 10px;
    border: 1px solid #d0d0d0;
    border-radius: 4px;
    font-size: 14px;
    font-family: inherit;
}

.filter-btn {
    padding: 10px 20px;
    background: #000;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 1px;
}

.filter-btn:hover {
    background: #333;
}

.clear-filters {
    padding: 10px 20px;
    background: #fff;
    color: #000;
    border: 1px solid #d0d0d0;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 1px;
}

.clear-filters:hover {
    background: #f5f5f5;
}

.results-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e0e0e0;
}

.results-count {
    font-weight: 600;
}

.no-results {
    text-align: center;
    padding: 60px 20px;
}

.no-results h2 {
    font-size: 1.5rem;
    margin-bottom: 15px;
    color: #666;
}

.no-results p {
    color: #888;
    margin-bottom: 20px;
}

.back-btn {
    display: inline-block;
    padding: 12px 30px;
    background: #000;
    color: #fff;
    text-decoration: none;
    text-transform: uppercase;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 1px;
    border-radius: 4px;
}

.back-btn:hover {
    background: #333;
}

.search-suggestions {
    margin-top: 20px;
    padding: 20px;
    background: #f0f8ff;
    border-radius: 8px;
}

.search-suggestions h3 {
    font-size: 14px;
    margin-bottom: 10px;
    font-weight: 600;
}

.search-suggestions ul {
    list-style: none;
    padding: 0;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.search-suggestions a {
    padding: 8px 16px;
    background: #fff;
    border: 1px solid #d0d0d0;
    border-radius: 20px;
    text-decoration: none;
    color: #333;
    font-size: 13px;
    transition: all 0.2s;
}

.search-suggestions a:hover {
    background: #000;
    color: #fff;
    border-color: #000;
}
</style>

<div class="search-page">
    <div class="search-header">
        <h1>
            <?php 
            if (!empty($searchQuery)) {
                echo 'Search Results for "' . htmlspecialchars($searchQuery) . '"';
            } else {
                echo 'All Products';
            }
            ?>
        </h1>
        <div class="search-info">
            Found <strong><?php echo $totalResults; ?></strong> <?php echo $totalResults === 1 ? 'product' : 'products'; ?>
        </div>
    </div>

    <!-- Advanced Filters -->
    <form method="GET" action="search.php" class="search-filters">
        <div class="filter-group">
            <label for="q">Search</label>
            <input type="text" id="q" name="q" placeholder="Product name..." value="<?php echo htmlspecialchars($searchQuery); ?>">
        </div>
        
        <div class="filter-group">
            <label for="cat">Category</label>
            <select id="cat" name="cat">
                <option value="">All Categories</option>
                <option value="men" <?php echo $category === 'men' ? 'selected' : ''; ?>>Men</option>
                <option value="women" <?php echo $category === 'women' ? 'selected' : ''; ?>>Women</option>
                <option value="apparel" <?php echo $category === 'apparel' ? 'selected' : ''; ?>>Clothing</option>
                <option value="headwear" <?php echo $category === 'headwear' ? 'selected' : ''; ?>>Accessories</option>
                <option value="sport" <?php echo $category === 'sport' ? 'selected' : ''; ?>>Sportswear</option>
            </select>
        </div>
        
        <div class="filter-group">
            <label for="price_min">Min Price</label>
            <input type="number" id="price_min" name="price_min" placeholder="$0" step="0.01" 
                   value="<?php echo $priceMin > 0 ? $priceMin : ''; ?>">
        </div>
        
        <div class="filter-group">
            <label for="price_max">Max Price</label>
            <input type="number" id="price_max" name="price_max" placeholder="$999" step="0.01"
                   value="<?php echo $priceMax < 999999 ? $priceMax : ''; ?>">
        </div>
        
        <div class="filter-group">
            <label for="sort">Sort By</label>
            <select id="sort" name="sort">
                <option value="relevance" <?php echo $sortBy === 'relevance' ? 'selected' : ''; ?>>Relevance</option>
                <option value="price-asc" <?php echo $sortBy === 'price-asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                <option value="price-desc" <?php echo $sortBy === 'price-desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                <option value="name-asc" <?php echo $sortBy === 'name-asc' ? 'selected' : ''; ?>>Name: A-Z</option>
                <option value="newest" <?php echo $sortBy === 'newest' ? 'selected' : ''; ?>>Newest First</option>
            </select>
        </div>
        
        <button type="submit" class="filter-btn">Apply Filters</button>
        <a href="search.php" class="clear-filters">Clear All</a>
    </form>

    <?php if ($totalResults > 0): ?>
        <!-- Products Grid -->
        <ul class="products grid" id="product-list" style="display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:24px; list-style:none; padding:0;">
            <?php while($row = $result->fetch_assoc()): 
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
                <li class="card" data-id="<?php echo $id; ?>" data-cat="<?php echo $cat; ?>" data-price="<?php echo $price; ?>" 
                    data-title="<?php echo $name; ?>" data-cond="<?php echo $cond; ?>" data-rating="<?php echo $rating; ?>" 
                    data-img="<?php echo $img; ?>">
                    <div style="position:relative; overflow:hidden;">
                        <img src="<?php echo $img; ?>" loading="lazy" class="product-image" alt="<?php echo $name; ?>">
                        <button type="button" class="fav-toggle"><i class="fa fa-heart"></i></button>
                        <button type="button" class="quick-add-btn">Add to Bag</button>
                    </div>
                    <div class="product-info">
                        <h3 class="product-title"><?php echo $name; ?></h3>
                        <div class="product-price">$<?php echo $price; ?></div>
                        <?php if ($cond === 'new'): ?>
                            <div style="font-size: 11px; color: #28a745; font-weight: 600; margin-top: 5px;">NEW ARRIVAL</div>
                        <?php elseif ($cond === 'sale'): ?>
                            <div style="font-size: 11px; color: #dc3545; font-weight: 600; margin-top: 5px;">ON SALE</div>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>

        <!-- Load More Button -->
        <div style="text-align:center; margin-top:60px; margin-bottom:40px;">
            <button id="load-more-btn" style="padding:16px 50px; border:2px solid #000; background:#fff; color:#000; text-transform:uppercase; font-size:13px; font-weight:700; cursor:pointer; letter-spacing:1.5px; transition:all 0.3s ease; border-radius:2px; position:relative; overflow:hidden;">
                <span style="position:relative; z-index:1;">Load More Products</span>
            </button>
        </div>

        
        <!-- Results Counter for JS to update -->
        <span id="results-count" style="display:none;"><?php echo $totalResults; ?></span>
    <?php else: ?>
        <!-- No Results -->
        <div class="no-results">
            <h2>No products found</h2>
            <p>We couldn't find any products matching your search criteria.</p>
            <a href="index.php" class="back-btn">Browse All Products</a>
            
            <div class="search-suggestions">
                <h3>Try searching for:</h3>
                <ul>
                    <li><a href="search.php?q=tshirt">T-Shirts</a></li>
                    <li><a href="search.php?q=cap">Caps</a></li>
                    <li><a href="search.php?q=blazer">Blazers</a></li>
                    <li><a href="search.php?cat=men">Men's Products</a></li>
                    <li><a href="search.php?cat=women">Women's Products</a></li>
                    <li><a href="search.php?sort=newest">New Arrivals</a></li>
                </ul>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
