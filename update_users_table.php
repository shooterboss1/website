<?php
require_once 'config.php';

// 1. Add ROLE column if it doesn't exist
$sql_check = "SHOW COLUMNS FROM users LIKE 'role'";
$result = $conn->query($sql_check);

if ($result->num_rows == 0) {
    // Add column
    $sql_alter = "ALTER TABLE users ADD COLUMN role ENUM('user', 'admin') DEFAULT 'user' AFTER password";
    if ($conn->query($sql_alter) === TRUE) {
        echo "✅ Column 'role' added successfully.<br>";
    } else {
        die("❌ Error adding column: " . $conn->error);
    }
} else {
    echo "ℹ️ Column 'role' already exists.<br>";
}

// 2. Create Default Admin Account
$admin_email = 'admin@nextgenfdm.com';
$admin_user = 'Admin';
$admin_pass = 'admin123';
$admin_hash = password_hash($admin_pass, PASSWORD_DEFAULT);
$role = 'admin';

// Check if admin exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $admin_email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    // Insert Admin
    $insert = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $insert->bind_param("ssss", $admin_user, $admin_email, $admin_hash, $role);
    
    if ($insert->execute()) {
        echo "✅ Admin account created.<br>";
        echo "📧 Email: $admin_email<br>";
        echo "🔑 Password: $admin_pass<br>";
    } else {
        echo "❌ Error creating admin: " . $insert->error . "<br>";
    }
    $insert->close();
} else {
    echo "ℹ️ Admin account already exists. Updating role to 'admin' just in case.<br>";
    $update = $conn->prepare("UPDATE users SET role = 'admin' WHERE email = ?");
    $update->bind_param("s", $admin_email);
    $update->execute();
    $update->close();
}

$stmt->close();
$conn->close();
?>
