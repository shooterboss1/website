<?php
/**
 * Security Utilities
 * Handles CSRF tokens, password hashing, and validation
 */

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Generate CSRF Token
 * Creates a unique token and stores it in session
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_tokens'])) {
        $_SESSION['csrf_tokens'] = [];
    }
    
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_tokens'][$token] = time();
    
    // Clean old tokens (older than 1 hour)
    foreach ($_SESSION['csrf_tokens'] as $t => $timestamp) {
        if (time() - $timestamp > 3600) {
            unset($_SESSION['csrf_tokens'][$t]);
        }
    }
    
    return $token;
}

/**
 * Get CSRF Token
 * Retrieves token from session or generates new one
 */
function getCSRFToken() {
    if (empty($_SESSION['csrf_tokens'])) {
        $_SESSION['csrf_tokens'] = [];
    }
    
    // Generate a new token if none exist or all are expired
    if (empty($_SESSION['csrf_tokens'])) {
        $token = generateCSRFToken();
        return $token;
    }
    
    // Return the most recent valid token
    $tokens = array_keys($_SESSION['csrf_tokens']);
    return $tokens[count($tokens) - 1];
}

/**
 * Validate CSRF Token
 * Checks if provided token matches session token
 */
function validateCSRFToken($token = null) {
    if ($token === null) {
        $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
    }
    
    if (empty($token) || empty($_SESSION['csrf_tokens'][$token])) {
        return false;
    }
    
    // Token is valid, remove it to prevent reuse
    unset($_SESSION['csrf_tokens'][$token]);
    return true;
}

/**
 * CSRF Token HTML Input
 * Generate hidden input for forms
 */
function csrfTokenInput() {
    $token = getCSRFToken();
    return '<input type="hidden" name="csrf_token" id="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES) . '">';
}

/**
 * Hash Password Securely
 * Uses bcrypt algorithm with strong salt
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Verify Password
 * Checks if password matches hash
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Sanitize Input
 * Removes potentially harmful characters
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate Email
 * Simple email validation
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Rate Limiting
 * Prevents brute force attacks on login, etc.
 */
function checkRateLimit($key, $maxAttempts = 5, $timeWindow = 300) {
    if (!isset($_SESSION['rate_limit'])) {
        $_SESSION['rate_limit'] = [];
    }
    
    if (!isset($_SESSION['rate_limit'][$key])) {
        $_SESSION['rate_limit'][$key] = ['attempts' => 0, 'first_attempt' => time()];
    }
    
    $limit = &$_SESSION['rate_limit'][$key];
    $timeElapsed = time() - $limit['first_attempt'];
    
    // Reset if time window has passed
    if ($timeElapsed > $timeWindow) {
        $limit = ['attempts' => 0, 'first_attempt' => time()];
    }
    
    $limit['attempts']++;
    
    return $limit['attempts'] <= $maxAttempts;
}

/**
 * Get Rate Limit Remaining
 * Returns remaining attempts for given key
 */
function getRateLimitRemaining($key, $maxAttempts = 5) {
    if (!isset($_SESSION['rate_limit'][$key])) {
        return $maxAttempts;
    }
    return max(0, $maxAttempts - $_SESSION['rate_limit'][$key]['attempts']);
}

/**
 * Log Security Event
 * Records potential security issues for monitoring
 */
function logSecurityEvent($eventType, $details = []) {
    $logFile = __DIR__ . '/../../logs/security.log';
    
    // Create logs directory if it doesn't exist
    if (!is_dir(dirname($logFile))) {
        mkdir(dirname($logFile), 0755, true);
    }
    
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'type' => $eventType,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        'details' => $details,
        'user_id' => $_SESSION['id'] ?? 'anonymous'
    ];
    
    file_put_contents($logFile, json_encode($logEntry) . "\n", FILE_APPEND);
}
?>
