<?php
session_start();
require_once 'config.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        // Prepare statement to find user by email or username
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $email, $email); // Allow login by email or username
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $username, $hashed_password, $role);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                // Password is correct, start session
                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = $id;
                $_SESSION["username"] = $username;
                $_SESSION["role"] = $role;

                // Redirect based on role
                if ($_SESSION["role"] === 'admin') {
                    header("location: admin.php");
                } else {
                    header("location: index.php");
                }
                exit;
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "No account found with that email/username.";
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
    <title>Sign In - NEXTGEN FDM</title>
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
        .error { color: red; background: #ffebee; }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="auth-container">
    <h2>Sign In</h2>
    
    <?php if($error): ?>
        <div class="message error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Email or Username</label>
            <input type="text" name="email" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" class="btn-submit">Sign In</button>
    </form>
    
</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>
