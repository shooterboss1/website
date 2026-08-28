<?php
require_once 'config.php';
$email = 'admin@wirwp.com';
$stmt = $conn->prepare("SELECT username, email, role FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    echo "SUCCESS: Admin logic verified for What's Real Shall Prosper.";
    echo "Username: " . $row['username'] . "\n";
    echo "Email: " . $row['email'] . "\n";
    echo "Role: " . $row['role'] . "\n";
} else {
    echo "FAILURE: Admin account not found.\n";
}
$stmt->close();
$conn->close();
?>

