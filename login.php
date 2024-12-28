<?php
session_start();
require 'db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    
    $sql = "SELECT * FROM users WHERE email = :email AND role = :role";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':role', $role);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        
        $insertSql = "INSERT INTO login_records (user_id, status) VALUES (:user_id, :status)";
        $insertStmt = $conn->prepare($insertSql);
        $status = 'successful'; 
        $insertStmt->bindParam(':user_id', $user['user_id']);
        $insertStmt->bindParam(':status', $status);
        $insertStmt->execute();

        
        switch ($user['role']) {
            case 'admin':
                echo "<script>alert('Admin Login successful! Welcome, " . htmlspecialchars($user['name']) . "'); window.location.href='admin_dashboard.php';</script>";
                break;
            case 'staff':
                echo "<script>alert('Staff Login successful! Welcome, " . htmlspecialchars($user['name']) . "'); window.location.href='staff_dashboard.php';</script>";
                break;
            case 'customer':
                echo "<script>alert('Customer Login successful! Welcome, " . htmlspecialchars($user['name']) . "'); window.location.href='customer_dashboard.php';</script>";
                break;
            default:
                echo "<script>alert('Invalid role.');</script>";
        }
        exit();
    } else {
        echo "<script>alert('Invalid email, password, or role.');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>

<header>
    <a href="index.html#home" class="logo">FitZone <span>Fitness</span></a>
    <div class='bx bx-menu' id="menu-icon"></div>
    <ul class="navbar">
        <li><a href="index.html#home">Home</a></li>
        <li><a href="index.html#services">Services</a></li>
        <li><a href="index.html#about">About</a></li> 
        <li><a href="index.html#plans">Packages</a></li>
    </ul>
    <div class="top-btn">
        <a href="login.php" class="nav-btn">Log In</a>
        <a href="signup.php" class="nav-btn">Sign Up</a>
    </div>
</header>

<div class="container">
    <div class="form-container" id="login-form">
        <h2>Login</h2>
        <form method="post" action="">
            <div class="input-field">
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-field password-field">
                <input type="password" name="password" id="login-password" placeholder="Password" required>
                <span class="toggle-password" onclick="togglePassword('login-password')">
                    <i class='bx bxs-hide'></i>
                </span>
            </div>
            <div class="input-field">
                <select name="role" required>
                    <option value="" disabled selected>Select Role</option>
                    <option value="customer">Customer</option>
                    <option value="staff">Staff</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="remember-me">
                <input type="checkbox" id="remember-me-login">
                <label for="remember-me-login">Remember Me</label>
            </div>
            <button type="submit" class="btn">Login</button>
            <p>Don't have an account? <a href="signup.php" class="login-link">Sign Up</a></p>
        </form>
    </div>
</div>

<footer>
    <div class="footer-container">
        <button class="btn mode-btn" onclick="location.href='instructor_login.php'">Staff Mode</button>
        <button class="btn mode-btn" onclick="location.href='admin.php'">Admin Mode</button>
        <div class="contact-info">
            <p>Contact: 0025485664 | Email: fitzone@gmail.com</p>
        </div>
    </div>
</footer>

<script src="login.js"></script>

</body>
</html>
