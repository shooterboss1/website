<?php
/**
 * AJAX endpoint for loading more products
 * Returns JSON array of products for infinite scroll / load more
 */

header('Content-Type: application/json');
ob_start(); // Buffer output to prevent warnings from breaking JSON
require_once 'config.php';
error_reporting(0); // Silence warnings in this specific endpoint

// Get parameters
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 12;
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$condition = isset($_GET['condition']) ? trim($_GET['condition']) : '';
$sortBy = isset($_GET['sort']) ? trim($_GET['sort']) : 'newest';
$priceRange = isset($_GET['price_range']) ? trim($_GET['price_range']) : '';
$minRating = isset($_GET['min_rating']) ? (float)$_GET['min_rating'] : 0;

// Validate limit (prevent abuse)
if ($limit > 50) $limit = 50;
if ($limit < 1) $limit = 12;

// Build SQL query
$sql = "SELECT * FROM products WHERE 1=1";
$params = [];
$types = "";

// Filter by category
if (!empty($category)) {
    // Special handling for Coming Soon / Unreleased
    if ($category === 'coming_soon') {
        $sql .= " AND (category = 'coming_soon' OR category = 'unreleased')";
    } else {
        $sql .= " AND category = ?";
        $params[] = $category;
        $types .= "s";
    }
}

// Filter by search term
if (!empty($search)) {
    $sql .= " AND (name LIKE ? OR category LIKE ?)";
    $searchTerm = "%{$search}%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "ss";
}

// Filter by condition
if (!empty($condition)) {
    $sql .= " AND product_condition = ?";
    $params[] = $condition;
    $types .= "s";
}

// Filter by Price Range
if (!empty($priceRange)) {
    $parts = explode('-', $priceRange);
    if (count($parts) === 2) {
        $sql .= " AND price >= ? AND price <= ?";
        $params[] = (float)$parts[0];
        $params[] = (float)$parts[1];
        $types .= "dd";
    }
}

// Filter by Rating
if ($minRating > 0) {
    $sql .= " AND rating >= ?";
    $params[] = $minRating;
    $types .= "d";
}

// Sorting
$allowedSorts = [
    'price-asc' => 'price ASC',
    'price-desc' => 'price DESC',
    'name-asc' => 'name ASC',
    'name-desc' => 'name DESC',
    'rating-desc' => 'rating DESC',
    'newest' => 'id DESC',
    'oldest' => 'id ASC'
];

$orderBy = isset($allowedSorts[$sortBy]) ? $allowedSorts[$sortBy] : 'id DESC';
$sql .= " ORDER BY " . $orderBy;

// Add limit and offset
$sql .= " LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= "ii";

// Execute query
try {
    $stmt = $conn->prepare($sql);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        // Check if image exists
        $imagePath = $row['image'];
        if (!file_exists(__DIR__ . '/' . $imagePath)) {
            $imagePath = 'https://via.placeholder.com/400x600?text=' . urlencode($row['name']);
        }
        
        $products[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'price' => (float)$row['price'],
            'category' => $row['category'],
            'image' => $imagePath,
            'rating' => (float)$row['rating'],
            'condition' => $row['product_condition']
        ];
    }
    
    // Get total count for this query (without limit)
    $countSql = str_replace("SELECT *", "SELECT COUNT(*) as total", 
                str_replace(" LIMIT ? OFFSET ?", "", $sql));
    $countStmt = $conn->prepare($countSql);
    
    if (!empty($params)) {
        // Remove last 2 params (limit and offset)
        $countParams = array_slice($params, 0, -2);
        $countTypes = substr($types, 0, -2);
        
        if (!empty($countParams)) {
            $countStmt->bind_param($countTypes, ...$countParams);
        }
    }
    
    $countStmt->execute();
    $totalResult = $countStmt->get_result();
    $totalRow = $totalResult->fetch_assoc();
    $totalProducts = (int)$totalRow['total'];
    
    // Response
    $response = [
        'success' => true,
        'products' => $products,
        'total' => $totalProducts,
        'offset' => $offset,
        'limit' => $limit,
        'hasMore' => ($offset + $limit) < $totalProducts,
        'loaded' => count($products)
    ];
    
} catch (Exception $e) {
    ob_end_clean(); // Discard buffer
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to load products',
        'message' => $e->getMessage()
    ]);
    exit;
}

$conn->close();
ob_end_clean(); 
echo json_encode($response);
?>
