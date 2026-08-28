<?php
require_once 'config.php';

$message = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if user exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $error = "Username or Email already exists.";
        } else {
            // Hash password and insert
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
            $insert->bind_param("sss", $username, $email, $hashed_password);
            
            if ($insert->execute()) {
                $message = "Account created successfully! <a href='index.php'>Go to Home</a>";
            } else {
                $error = "Error: " . $conn->error;
            }
            $insert->close();
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - What's Real Shall Prosper</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .auth-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 40px;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
        }
        .auth-container h2 {
            margin-bottom: 24px;
            font-size: 24px;
            font-weight: 700;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 600;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            font-size: 14px;
        }
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: #000;
            color: #fff;
            border: none;
            font-weight: 700;
            text-transform: uppercase;
            cursor: pointer;
            font-size: 12px;
            letter-spacing: 1px;
        }
        .btn-submit:hover {
            background: #333;
        }
        .message {
            margin-bottom: 20px;
            padding: 10px;
            font-size: 13px;
        }
        .success { color: green; background: #e8f5e9; }
        .error { color: red; background: #ffebee; }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="auth-container">
    <h2>Create Account</h2>
    
    <?php if($message): ?>
        <div class="message success"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <?php if($error): ?>
        <div class="message error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn-submit">Sign Up</button>
    </form>
    
    <p style="margin-top: 20px; font-size: 13px; color: #707070;">
        Already have an account? <a href="signin.php" style="text-decoration: underline; color: #000;">Sign In</a>
    </p>
</div>

</body>
</html>

