<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEXTGEN FDM - Premium Clothing</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="chatbot.css">
    <!-- FontAwesome for icons (optional, could use SVGs) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<header class="navbar">
    <?php 
    $current_page = basename($_SERVER['PHP_SELF']);
    if ($current_page !== 'index.php' && $current_page !== ''): 
    ?>
    <!-- Pro Back Button (Mobile Only) -->
    <a href="javascript:history.back()" class="back-btn" title="Go Back">
        <i class="fa fa-chevron-left"></i>
        <span>Back</span>
    </a>
    <?php endif; ?>

    <a href="index.php" class="logo">
        <img src="images/logo.jpeg?v=1.2" alt="NEXTGEN FDM">
    </a>

    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
        <span class="nav-separator">|</span>
        <a href="men.php">Men</a>
        <a href="women.php">Women</a>
    </div>

    <!-- Search Bar -->
    <div class="search-bar">
        <form action="search.php" method="GET" style="display:flex;align-items:center;">
            <button type="submit" class="search-btn"><i class="fa fa-search"></i></button>
            <input type="text" name="q" id="site-search" placeholder="Search products..." autocomplete="off">
        </form>
    </div>

    <div class="nav-icons">
        <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
            <!-- Admin Dashboard Link -->
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="admin.php" class="nav-icon" title="Admin Dashboard"><i class="fa fa-gauge-high"></i> Dashboard</a>
            <?php endif; ?>
            
            <!-- Show Logout if logged in -->
            <a href="logout.php" class="nav-icon" title="Sign Out (<?php echo htmlspecialchars($_SESSION['username']); ?>)"><i class="fa fa-sign-out-alt"></i></a>
        <?php else: ?>
            <!-- Show Sign In and Sign Up if not logged in -->
            <a href="signin.php" class="nav-icon" title="Sign In"><i class="fa fa-user"></i> Sign In</a>
            <a href="signup.php" class="nav-icon" title="Create Account" style="background:#000;color:#fff;padding:6px 12px;border-radius:4px;font-size:12px;font-weight:700;letter-spacing:0.5px;text-transform:uppercase;">Create Account</a>
        <?php endif; ?>
        <a href="#" class="nav-icon" id="fav-btn"><i class="fa fa-heart"></i><span id="fav-badge" style="font-size:10px; vertical-align:top; display:none">0</span></a>
        <a href="#" class="nav-icon" id="cart-btn">
            <i class="fa fa-shopping-bag"></i>
            <span id="cart-badge" style="font-size:10px; vertical-align:top;">0</span>
        </a>
    </div>
</header>
