<?php
/**
 * Configuration File - Loads from .env
 * DO NOT COMMIT .env FILE (Use .gitignore)
 */

// Load environment variables from .env file
function loadEnv($filePath = __DIR__ . '/.env') {
    if (!file_exists($filePath)) {
        die("Error: .env file not found at " . $filePath);
    }
    
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (str_starts_with(trim($line), '#')) continue;
        
        // Parse KEY=VALUE
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            $_ENV[$key] = $value;
            putenv("{$key}={$value}");
        }
    }
}

// Load environment variables
loadEnv();

// Determine environment
$app_env = $_ENV['APP_ENV'] ?? 'production';
$is_local = ($app_env === 'development');

// Database Configuration
define('DB_SERVER', $_ENV['DB_SERVER'] ?? 'localhost');
define('DB_USERNAME', $_ENV['DB_USERNAME'] ?? 'root');
define('DB_PASSWORD', $_ENV['DB_PASSWORD'] ?? '');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'clothing');
define('BASE_URL', $_ENV['BASE_URL'] ?? 'http://localhost/ClothingBrandwebsite/');

// Stripe Configuration
define('STRIPE_PUBLISHABLE_KEY', $_ENV['STRIPE_PUBLISHABLE_KEY'] ?? '');
define('STRIPE_SECRET_KEY', $_ENV['STRIPE_SECRET_KEY'] ?? '');

// PayPal Configuration
define('PAYPAL_CLIENT_ID', $_ENV['PAYPAL_CLIENT_ID'] ?? '');
define('PAYPAL_SECRET', $_ENV['PAYPAL_SECRET'] ?? '');

// Email Configuration
define('MAIL_HOST', $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com');
define('MAIL_PORT', $_ENV['MAIL_PORT'] ?? '587');
define('MAIL_USERNAME', $_ENV['MAIL_USERNAME'] ?? '');
define('MAIL_PASSWORD', $_ENV['MAIL_PASSWORD'] ?? '');
define('MAIL_FROM_ADDRESS', $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@nextgenfdm.com');

// Business Info for Emails
define('ADMIN_EMAIL', $_ENV['ADMIN_EMAIL'] ?? 'admin@nextgenfdm.com');
define('BRAND_NAME', $_ENV['BRAND_NAME'] ?? 'NEXTGEN FDM');

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

// Load security utilities
require_once __DIR__ . '/includes/security.php';

// Load email service
require_once __DIR__ . '/includes/EmailService.php';
?>
