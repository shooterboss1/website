<?php
session_start();
require_once 'config.php';

// --- AUTHENTICATION CHECK ---
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("location: index.php");
    exit;
}

// --- ACTION HANDLING ---
$message = '';
$error = '';

/** Handle Image Upload Helper */
function uploadImage($file) {
    $target_dir = "images/";
    $filename = basename($file["name"]);
    // Sanitize filename
    $filename = preg_replace("/[^a-zA-Z0-9.]/", "_", $filename);
    $target_file = $target_dir . $filename;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Check if image file is a actual image
    $check = getimagesize($file["tmp_name"]);
    if($check === false) return ["error" => "File is not an image."];
    
    // Check file size (5MB max)
    if ($file["size"] > 5000000) return ["error" => "Sorry, your file is too large."];
    
    // Allow certain file formats
    if(!in_array($imageFileType, ["jpg", "png", "jpeg", "webp"])) {
        return ["error" => "Sorry, only JPG, JPEG, PNG & WEBP files are allowed."];
    }
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ["success" => $target_file]; // Return relative path
    } else {
        return ["error" => "Sorry, there was an error uploading your file."];
    }
}

// 1. DELETE PRODUCT
if (isset($_GET['action']) && $_GET['action'] == 'delete_product' && isset($_GET['id'])) {
    $prod_id = intval($_GET['id']);
    // Optional: Delete image file logic could go here if we wanted to clean up
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $prod_id);
    if ($stmt->execute()) {
        $message = "Material/Product deleted successfully.";
    } else {
        $error = "Failed to delete product.";
    }
}

// 2. UPDATE ORDER STATUS
if (isset($_GET['action']) && $_GET['action'] == 'update_status' && isset($_GET['id']) && isset($_GET['status'])) {
    $order_id = intval($_GET['id']);
    $status = $_GET['status'];
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);
    if ($stmt->execute()) {
        $message = "Order #$order_id status updated.";
    }
}

// 3. SET PRODUCT CONDITION (Featured/New)
if (isset($_GET['action']) && $_GET['action'] == 'set_condition' && isset($_GET['id']) && isset($_GET['cond'])) {
    $pid = intval($_GET['id']);
    $cond = $_GET['cond'];
    $stmt = $conn->prepare("UPDATE products SET product_condition = ? WHERE id = ?");
    $stmt->bind_param("si", $cond, $pid);
    if ($stmt->execute()) {
        $message = "Product updated to " . ucfirst($cond);
    }
}

// 3. ADD NEW PRODUCT (MATERIAL)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    // Validate CSRF token
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = "Security validation failed. Please try again.";
    } else {
        $name = sanitizeInput($_POST['name'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $category = sanitizeInput($_POST['category'] ?? '');
        $rating = floatval($_POST['rating'] ?? 5.0);
        $condition = sanitizeInput($_POST['condition'] ?? '');
        
        // Handle Image Upload
        if(isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $upload = uploadImage($_FILES['product_image']);
        if(isset($upload['success'])) {
            $image_path = $upload['success'];
            
            // Insert into DB
            $stmt = $conn->prepare("INSERT INTO products (name, price, category, image, rating, product_condition) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sdssis", $name, $price, $category, $image_path, $rating, $condition);
            
            if ($stmt->execute()) {
                $message = "New material '$name' added successfully!";
            } else {
                $error = "Database Error: " . $conn->error;
            }
        } else {
            $error = $upload['error'];
        }
    } else {
        // Fallback for manual path entry if needed (or error)
        $error = "Please upload a product image.";
    }
    }
}

// --- VIEW LOGIC ---
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Fetch Global Stats
$total_sales = $conn->query("SELECT SUM(total_amount) as total FROM orders")->fetch_assoc()['total'] ?? 0;
$total_orders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'] ?? 0;
$total_products = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'] ?? 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEXTGEN FDM Admin Portal</title>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Admin Style -->
    <link rel="stylesheet" href="style_admin.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="admin-layout">
    <!-- SIDEBAR -->
    <div class="admin-sidebar">
        <div class="brand">
            <i class="fas fa-layer-group" style="font-size: 20px;"></i>
            <span class="brand-text">NEXTGEN FDM</span>
        </div>
        
        <nav class="admin-nav">
            <a href="?page=dashboard" class="<?php echo $page == 'dashboard' ? 'active' : ''; ?>">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
            <a href="?page=products" class="<?php echo $page == 'products' ? 'active' : ''; ?>">
                <i class="fas fa-tshirt"></i> Materials / Products
            </a>
            <a href="?page=orders" class="<?php echo $page == 'orders' ? 'active' : ''; ?>">
                <i class="fas fa-shopping-bag"></i> Orders
            </a>
            <a href="?page=add_product" class="<?php echo $page == 'add_product' ? 'active' : ''; ?>">
                <i class="fas fa-plus-circle"></i> Add New Material
            </a>
        </nav>

        <div class="bottom-nav">
            <nav class="admin-nav">
                <a href="index.php" target="_blank">
                    <i class="fas fa-external-link-alt"></i> View Website
                </a>
                <a href="logout.php" style="color: #dc3545;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="admin-main">
        
        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="page-title"><?php echo $page == 'add_product' ? 'Add New Material' : ($page == 'products' ? 'Materials Management' : ucfirst($page)); ?></h1>
                <p style="color: #888; margin-top: 5px;">Manage your store inventory and orders</p>
            </div>
            <div class="admin-user-profile">
                <div style="text-align: right;">
                    <div style="font-weight: 600;"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
                    <div style="font-size: 12px; color: #888;">Administrator</div>
                </div>
                <div class="admin-avatar">
                    <?php echo substr($_SESSION['username'], 0, 1); ?>
                </div>
            </div>
        </div>

        <!-- NOTIFICATIONS -->
        <?php if($message): ?>
            <div style="background: #e6f9ed; color: #155724; padding: 15px 20px; border-radius: 8px; margin-bottom: 30px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-check-circle"></i> <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div style="background: #fdecea; color: #721c24; padding: 15px 20px; border-radius: 8px; margin-bottom: 30px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- DASHBOARD PAGE -->
        <?php if($page == 'dashboard'): ?>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
                    <div class="stat-info">
                        <span class="value">$<?php echo number_format($total_sales, 2); ?></span>
                        <span class="label">Total Revenue</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-shopping-cart"></i></div>
                    <div class="stat-info">
                        <span class="value"><?php echo $total_orders; ?></span>
                        <span class="label">Total Orders</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-box-open"></i></div>
                    <div class="stat-info">
                        <span class="value"><?php echo $total_products; ?></span>
                        <span class="label">Active Materials</span>
                    </div>
                </div>
            </div>

            <div class="table-card">
                <div class="table-header">
                    <h3>Recent Orders</h3>
                    <a href="?page=orders" style="font-size: 13px; color: #000; font-weight: 600;">View All</a>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $rec_orders = $conn->query("SELECT * FROM orders ORDER BY order_date DESC LIMIT 5");
                        if($rec_orders->num_rows > 0):
                            while($o = $rec_orders->fetch_assoc()):
                        ?>
                        <tr>
                            <td style="font-family: monospace;">#<?php echo $o['id']; ?></td>
                            <td><?php echo htmlspecialchars($o['customer_name']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($o['order_date'])); ?></td>
                            <td style="font-weight: 600;">$<?php echo number_format($o['total_amount'], 2); ?></td>
                            <td><span class="status-badge status-<?php echo strtolower($o['status']); ?>"><?php echo $o['status']; ?></span></td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="5" style="text-align:center; padding: 30px; color: #999;">No orders yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- PRODUCTS PAGE -->
        <?php if($page == 'products'): ?>
            <div class="table-card">
                <div class="table-header">
                    <h3>All Materials (<?php echo $total_products; ?>)</h3>
                    <a href="?page=add_product" class="btn-add"><i class="fas fa-plus"></i> Add Material</a>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th width="80">Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Condition</th>
                            <th width="100">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $prods = $conn->query("SELECT * FROM products ORDER BY id DESC");
                        if($prods->num_rows > 0):
                            while($p = $prods->fetch_assoc()):
                        ?>
                        <tr>
                            <td>
                                <div style="width: 50px; height: 50px; background: #f4f4f4; border-radius: 8px; overflow: hidden;">
                                    <img src="<?php echo htmlspecialchars($p['image']); ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='images/placeholder.jpg'">
                                </div>
                            </td>
                            <td style="font-weight: 500; font-size: 15px;"><?php echo htmlspecialchars($p['name']); ?></td>
                            <td><span style="background: #f0f0f0; padding: 4px 10px; border-radius: 4px; font-size: 12px;"><?php echo ucfirst($p['category']); ?></span></td>
                            <td style="font-weight: 600;">$<?php echo number_format($p['price'], 2); ?></td>
                            <td><span class="status-badge status-<?php echo $p['product_condition'] == 'new' ? 'new' : 'sale'; ?>"><?php echo ucfirst($p['product_condition']); ?></span></td>
                            <td style="display:flex; gap:5px;">
                                <!-- Toggle Featured -->
                                <?php if($p['product_condition'] == 'featured'): ?>
                                    <a href="?action=set_condition&id=<?php echo $p['id']; ?>&cond=new&page=products" class="action-btn" title="Remove Featured" style="color: #f0ad4e; border-color: #f0ad4e;"><i class="fas fa-star"></i></a>
                                <?php else: ?>
                                    <a href="?action=set_condition&id=<?php echo $p['id']; ?>&cond=featured&page=products" class="action-btn" title="Make Featured" style="color: #ccc;"><i class="far fa-star"></i></a>
                                <?php endif; ?>
                                
                                <a href="?action=delete_product&id=<?php echo $p['id']; ?>&page=products" class="action-btn delete" onclick="return confirm('Are you sure you want to delete this material? This cannot be undone.');" title="Delete Material">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="6" style="text-align:center; padding: 50px; color: #888;">No products found in inventory.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- ORDERS PAGE -->
        <?php if($page == 'orders'): ?>
            <div class="table-card">
                <div class="table-header">
                    <h3>All Orders</h3>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer info</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $orders = $conn->query("SELECT * FROM orders ORDER BY order_date DESC");
                         if($orders->num_rows > 0):
                            while($o = $orders->fetch_assoc()):
                                $oid = $o['id'];
                                $ic = $conn->query("SELECT COUNT(*) as c FROM order_items WHERE order_id=$oid")->fetch_assoc()['c'];
                        ?>
                        <tr>
                            <td>#<?php echo $o['id']; ?></td>
                            <td>
                                <div style="font-weight: 600;"><?php echo htmlspecialchars($o['customer_name']); ?></div>
                                <div style="font-size: 12px; color: #888;"><?php echo htmlspecialchars($o['customer_email']); ?></div>
                            </td>
                            <td><?php echo $ic; ?> items</td>
                            <td style="font-weight: 600;">$<?php echo number_format($o['total_amount'], 2); ?></td>
                            <td><span class="status-badge status-<?php echo strtolower($o['status']); ?>"><?php echo $o['status']; ?></span></td>
                            <td><?php echo date('M d H:i', strtotime($o['order_date'])); ?></td>
                            <td>
                                <?php if($o['status'] != 'shipped'): ?>
                                    <a href="?action=update_status&id=<?php echo $o['id']; ?>&status=shipped&page=orders" class="action-btn" title="Mark Shipped" style="color: #2196f3; border-color: #2196f3;"><i class="fas fa-truck"></i></a>
                                <?php else: ?>
                                    <a href="?action=update_status&id=<?php echo $o['id']; ?>&status=completed&page=orders" class="action-btn" title="Mark Complete" style="color: #28a745; border-color: #28a745;"><i class="fas fa-check"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="7" style="text-align:center; padding: 50px;">No orders found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- ADD PRODUCT PAGE -->
        <?php if($page == 'add_product'): ?>
            <form action="?page=add_product" method="POST" enctype="multipart/form-data" class="form-grid">
                <?php echo csrfTokenInput(); ?>
                
                <!-- Main Details -->
                <div class="form-section">
                    <h3 style="margin-top: 0; margin-bottom: 25px;">Basic Information</h3>
                    
                    <div class="input-group">
                        <label class="input-label">Material Name</label>
                        <input type="text" name="name" class="input-field" placeholder="e.g. Signature Cotton Tee" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                        <div class="input-group">
                            <label class="input-label">Price ($)</label>
                            <input type="number" step="0.01" name="price" class="input-field" placeholder="0.00" required>
                        </div>
                        <div class="input-group">
                            <label class="input-label">Product Rating (1-5)</label>
                            <input type="number" step="0.1" min="1" max="5" name="rating" value="5.0" class="input-field">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                        <div class="input-group">
                            <label class="input-label">Category</label>
                            <select name="category" class="input-field">
                                <option value="men">Men</option>
                                <option value="women">Women</option>
                                <option value="apparel">Apparel</option>
                                <option value="headwear">Headwear</option>
                                <option value="accessories">Accessories</option>
                            </select>
                        </div>
                         <div class="input-group">
                            <label class="input-label">Condition</label>
                            <select name="condition" class="input-field">
                                <option value="new">Brand New</option>
                                <option value="sale">On Sale</option>
                                <option value="featured">Featured Item</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Image Upload -->
                <div class="form-section" style="height: fit-content;">
                    <h3 style="margin-top: 0; margin-bottom: 25px;">Product Image</h3>
                    
                    <div class="file-upload-wrapper" onclick="document.getElementById('file-input').click()">
                        <div style="font-size: 40px; color: #ccc; margin-bottom: 15px;"><i class="fas fa-cloud-upload-alt"></i></div>
                        <p style="margin: 0; font-weight: 500;">Click to Upload Image</p>
                        <p style="margin: 5px 0 0; font-size: 12px; color: #999;">JPG, PNG or WEBP (Max 5MB)</p>
                        <input type="file" name="product_image" id="file-input" style="display: none;" accept="image/*" required onchange="document.getElementById('file-name').innerText = this.files[0].name">
                    </div>
                    <div id="file-name" style="margin-top: 15px; font-size: 13px; color: #555; text-align: center;"></div>
                    
                    <div style="margin-top: 30px;">
                        <button type="submit" name="add_product" class="btn-primary">
                            <i class="fas fa-plus" style="margin-right: 8px;"></i> Publish Material
                        </button>
                    </div>
                </div>

            </form>
        <?php endif; ?>

    </div>
</div>

</body>
</html>
