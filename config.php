<?php
// Database Configuration
// ----------------------
// When launching worldwide, update these with your hosting provider's details.

// Determine if running on localhost or live server
$server_name = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost';
$is_local = ($server_name == 'localhost' || $server_name == '127.0.0.1');

if ($is_local) {
    // Local Development Settings
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', '');
    define('DB_NAME', 'clothing');
    define('BASE_URL', 'http://localhost/ClothingBrandwebsite/');
    
    // Stripe Test Keys (Replace with your own from stripe.com)
    define('STRIPE_PUBLISHABLE_KEY', 'pk_test_51QSTjSP3zOWaSKh7yWr0VXMDVHSMzmz3oqLQSEjODqXNyFr21iBF7yxQEgUoOJ8bCx61PzUTmDOxJ3WZVjl8l9So00k9e5xVpt');
    define('STRIPE_SECRET_KEY', 'sk_test_YOUR_SECRET_KEY_HERE'); // Needed for backend processing if upgraded
} else {
    // Live Production Settings (FILL THIS IN WHEN UPLOADING)
    define('DB_SERVER', 'localhost');      // Often 'localhost' on shared hosting too
    define('DB_USERNAME', 'YOUR_DB_USER'); // Created in your hosting panel
    define('DB_PASSWORD', 'YOUR_DB_PASS'); // Created in your hosting panel
    define('DB_NAME', 'YOUR_DB_NAME');     // Created in your hosting panel
    define('BASE_URL', 'https://www.yourdomain.com/'); // Your actual domain
    
    // Stripe Live Keys
    define('STRIPE_PUBLISHABLE_KEY', 'pk_live_YOUR_LIVE_KEY_HERE');
    define('STRIPE_SECRET_KEY', 'sk_live_YOUR_LIVE_SECRET_KEY_HERE');
}

// Business Info for Emails
define('ADMIN_EMAIL', 'admin@nextgenfdm.com');
define('BRAND_NAME', 'NEXTGEN FDM');

// Create Database Connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    // In production, don't show specific error details to users for security
    if ($is_local) {
        die("Connection failed: " . $conn->connect_error);
    } else {
        die("System currently unavailable. Please try again later.");
    }
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");
?>
