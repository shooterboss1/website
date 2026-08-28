<?php
/**
 * Debug script to clear session and test CSRF tokens
 */
session_start();

// Clear all session data
session_unset();
session_destroy();

// Start fresh session
session_start();

echo "<h2>Session Reset Complete</h2>";
echo "<p>All session data has been cleared.</p>";
echo "<p>Please try logging in again: <a href='signin.php'>Sign In</a></p>";

// Test CSRF token generation
require_once 'includes/security.php';

echo "<h3>CSRF Token Test:</h3>";
$token = getCSRFToken();
echo "<p>Generated token: " . substr($token, 0, 10) . "...</p>";
echo "<p>Token stored in session: " . (isset($_SESSION['csrf_tokens'][$token]) ? 'YES' : 'NO') . "</p>";
?>
