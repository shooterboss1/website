<?php
/**
 * Configuration File - Loads from .env (optional) or System Environment Variables
 * DO NOT COMMIT .env FILE (Use .gitignore)
 */

// Polyfills for PHP < 8.0 compatibility
if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle) {
        return (string)$needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}
if (!function_exists('str_ends_with')) {
    function str_ends_with($haystack, $needle) {
        return $needle === '' || substr($haystack, -strlen($needle)) === $needle;
    }
}

/**
 * Helper to fetch environment variables from getenv(), $_ENV, or $_SERVER
 */
function getEnvVar($key, $default = '') {
    $keys = is_array($key) ? $key : [$key];
    foreach ($keys as $k) {
        $val = getenv($k);
        if ($val !== false && $val !== '') {
            return $val;
        }
        if (isset($_ENV[$k]) && $_ENV[$k] !== '') {
            return $_ENV[$k];
        }
        if (isset($_SERVER[$k]) && $_SERVER[$k] !== '') {
            return $_SERVER[$k];
        }
    }
    return $default;
}

/**
 * Load environment variables from .env file if present
 */
function loadEnv($filePath = __DIR__ . '/.env') {
    if (!file_exists($filePath)) {
        return; // .env is optional (especially in cloud production environments like Render)
    }
    
    $lines = @file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }
    
    foreach ($lines as $line) {
        $trimmed = trim($line);
        // Skip comments and empty lines
        if (empty($trimmed) || str_starts_with($trimmed, '#')) {
            continue;
        }
        
        // Parse KEY=VALUE
        if (strpos($trimmed, '=') !== false) {
            list($key, $value) = explode('=', $trimmed, 2);
            $key = trim($key);
            $value = trim($value);
            // Trim quotes if present
            if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                $value = substr($value, 1, -1);
            }
            
            if (getenv($key) === false) {
                putenv("{$key}={$value}");
            }
            if (!isset($_ENV[$key])) {
                $_ENV[$key] = $value;
            }
            if (!isset($_SERVER[$key])) {
                $_SERVER[$key] = $value;
            }
        }
    }
}

// Load environment variables from .env file if available
loadEnv();

// Auto-parse connection URL if provided (e.g. Aiven, Render, Railway, Heroku)
$db_url = getEnvVar(['DATABASE_URL', 'MYSQL_URL', 'CLEARDB_DATABASE_URL', 'AIVEN_DB_URL']);
if (!empty($db_url)) {
    $parsed = parse_url($db_url);
    if ($parsed && isset($parsed['host'])) {
        if (getenv('DB_SERVER') === false && !isset($_ENV['DB_SERVER'])) {
            putenv("DB_SERVER=" . $parsed['host']);
            $_ENV['DB_SERVER'] = $parsed['host'];
        }
        if (getenv('DB_USERNAME') === false && !isset($_ENV['DB_USERNAME'])) {
            putenv("DB_USERNAME=" . ($parsed['user'] ?? 'root'));
            $_ENV['DB_USERNAME'] = $parsed['user'] ?? 'root';
        }
        if (getenv('DB_PASSWORD') === false && !isset($_ENV['DB_PASSWORD'])) {
            putenv("DB_PASSWORD=" . ($parsed['pass'] ?? ''));
            $_ENV['DB_PASSWORD'] = $parsed['pass'] ?? '';
        }
        if (getenv('DB_NAME') === false && !isset($_ENV['DB_NAME'])) {
            $dbName = isset($parsed['path']) ? ltrim($parsed['path'], '/') : 'clothing';
            putenv("DB_NAME=" . $dbName);
            $_ENV['DB_NAME'] = $dbName;
        }
        if (getenv('DB_PORT') === false && !isset($_ENV['DB_PORT']) && isset($parsed['port'])) {
            putenv("DB_PORT=" . $parsed['port']);
            $_ENV['DB_PORT'] = (string)$parsed['port'];
        }
    }
}

// Database Configuration
define('DB_SERVER', getEnvVar(['DB_SERVER', 'DB_HOST', 'MYSQLHOST'], 'localhost'));
define('DB_USERNAME', getEnvVar(['DB_USERNAME', 'DB_USER', 'MYSQLUSER'], 'root'));
define('DB_PASSWORD', getEnvVar(['DB_PASSWORD', 'DB_PASS', 'MYSQLPASSWORD'], ''));
define('DB_NAME', getEnvVar(['DB_NAME', 'DB_DATABASE', 'MYSQLDATABASE'], 'clothing'));
define('DB_PORT', getEnvVar(['DB_PORT', 'MYSQLPORT'], '3306'));
define('BASE_URL', getEnvVar('BASE_URL', 'http://localhost/ClothingBrandwebsite/'));

// Stripe Configuration
define('STRIPE_PUBLISHABLE_KEY', getEnvVar('STRIPE_PUBLISHABLE_KEY', ''));
define('STRIPE_SECRET_KEY', getEnvVar('STRIPE_SECRET_KEY', ''));

// PayPal Configuration
define('PAYPAL_CLIENT_ID', getEnvVar('PAYPAL_CLIENT_ID', ''));
define('PAYPAL_SECRET', getEnvVar('PAYPAL_SECRET', ''));

// Email Configuration
define('MAIL_HOST', getEnvVar('MAIL_HOST', 'smtp.gmail.com'));
define('MAIL_PORT', getEnvVar('MAIL_PORT', '587'));
define('MAIL_USERNAME', getEnvVar('MAIL_USERNAME', ''));
define('MAIL_PASSWORD', getEnvVar('MAIL_PASSWORD', ''));
define('MAIL_FROM_ADDRESS', getEnvVar('MAIL_FROM_ADDRESS', 'noreply@nextgenfdm.com'));

// Business Info for Emails
define('ADMIN_EMAIL', getEnvVar('ADMIN_EMAIL', 'admin@nextgenfdm.com'));
define('BRAND_NAME', getEnvVar('BRAND_NAME', "What's Real Shall Prosper"));

// Create Database Connection
$port = (int) DB_PORT;
$conn = mysqli_init();

// Detect if SSL connection is required (Aiven MySQL requires SSL, or DB_SSL=true)
$db_ssl = strtolower(getEnvVar(['DB_SSL', 'MYSQL_SSL'], 'false'));
$is_aiven = (strpos(DB_SERVER, 'aivencloud.com') !== false);
$has_ssl_flag = (!empty($db_url) && (strpos(strtolower($db_url), 'ssl-mode=required') !== false || strpos(strtolower($db_url), 'ssl=true') !== false));
$use_ssl = ($db_ssl === 'true' || $db_ssl === '1' || $is_aiven || $has_ssl_flag);

if ($use_ssl) {
    $ca_file = getEnvVar(['MYSQL_ATTR_SSL_CA', 'MYSQL_SSL_CA'], null);
    $conn->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);
    $conn->ssl_set(NULL, NULL, !empty($ca_file) ? $ca_file : NULL, NULL, NULL);
    @$conn->real_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, $port, NULL, MYSQLI_CLIENT_SSL);
} else {
    @$conn->real_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, $port);
}

// Check connection
if ($conn->connect_error) {
    if ($is_local) {
        die("Connection failed: " . $conn->connect_error);
    } else {
        error_log("Database Connection Error: " . $conn->connect_error);
        die("Database connection failed: " . $conn->connect_error);
    }
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");

// Load security utilities
require_once __DIR__ . '/includes/security.php';

// Load email service
require_once __DIR__ . '/includes/EmailService.php';
?>

