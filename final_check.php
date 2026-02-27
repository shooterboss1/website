<?php
// Fix for missing role column if the previous script failed
require_once 'config.php';

// Check if role column exists
$result = $conn->query("SHOW COLUMNS FROM users LIKE 'role'");
if ($result->num_rows == 0) {
    echo "Adding missing role column...<br>";
    $sql = "ALTER TABLE users ADD COLUMN role ENUM('user', 'admin') DEFAULT 'user' AFTER password";
    if ($conn->query($sql) === TRUE) {
        echo "✅ 'role' column added successfully.<br>";
    } else {
        echo "❌ Error adding 'role' column: " . $conn->error . "<br>";
    }
} else {
    echo "✅ 'role' column already exists.<br>";
}

// Check admin user
$admin_email = 'admin@nextgenfdm.com';
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $admin_email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    echo "Creating admin account...<br>";
    $u = 'Admin';
    $p = password_hash('admin123', PASSWORD_DEFAULT);
    $r = 'admin';
    $ins = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $ins->bind_param("ssss", $u, $admin_email, $p, $r);
    $ins->execute();
    echo "✅ Admin account created (admin@nextgenfdm.com / admin123).<br>";
} else {
    echo "✅ Admin account exists.<br>";
}

// Check logout.php content
if (!file_exists('logout.php')) {
    echo "Creating logout.php...<br>";
    $content = "<?php\nsession_start();\n\$_SESSION = array();\nsession_destroy();\nheader('location: signin.php');\nexit;\n?>";
    file_put_contents('logout.php', $content);
    echo "✅ logout.php created.<br>";
}

echo "<h3>Final Backend Check Complete.</h3>";
echo "<a href='index.php'>Go to Homepage</a>";
?>
