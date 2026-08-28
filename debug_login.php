<?php
/**
 * Debug login script to test authentication
 */
require_once 'config.php';

echo "<h2>Login Debug Test</h2>";

// Check if users exist
$users_query = "SELECT id, username, email, role FROM users";
$result = $conn->query($users_query);

if ($result && $result->num_rows > 0) {
    echo "<h3>Existing Users:</h3>";
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>ID: {$row['id']}, Username: {$row['username']}, Email: {$row['email']}, Role: {$row['role']}</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color: red;'>No users found in database!</p>";
    echo "<p>You need to create an account first: <a href='signup.php'>Sign Up</a></p>";
}

// Test CSRF token
echo "<h3>CSRF Token Test:</h3>";
require_once 'includes/security.php';

// Clear session first
session_unset();
session_destroy();
session_start();

$token = getCSRFToken();
echo "<p>Generated CSRF token: " . substr($token, 0, 20) . "...</p>";
echo "<p>Token in session: " . (isset($_SESSION['csrf_tokens'][$token]) ? '✅ YES' : '❌ NO') . "</p>";

// Test validation
$validate_result = validateCSRFToken($token);
echo "<p>Token validation: " . ($validate_result ? '✅ PASS' : '❌ FAIL') . "</p>";

echo "<hr>";
echo "<p><a href='signin.php'>Try Login Again</a> | <a href='signup.php'>Create Account</a></p>";
?>
