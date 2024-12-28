<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check_sql = "SELECT COUNT(*) FROM users WHERE email = :email";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bindParam(':email', $email);
    $check_stmt->execute();
    $count = $check_stmt->fetchColumn();

    if ($count > 0) {
        echo "<script>alert('Email already exists. Please choose a different email address.');</script>";
    } else {
        $sql = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->execute();

        echo "<script>alert('Account created successfully! Please login to continue.'); window.location.href='login.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="login.css">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css'>
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
    <div class="form-container" id="signup-form">
        <h2>Sign Up</h2>
        <form method="post" action="">
            <div class="input-field">
                <input type="text" name="name" placeholder="Name" required>
            </div>
            <div class="input-field">
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-field password-field">
                <input type="password" name="password" id="signup-password" placeholder="Password" required>
                <span class="toggle-password" onclick="togglePassword('signup-password')">
                    <i class='bx bxs-hide'></i>
                </span>
            </div>
            
            <button type="submit" class="btn">Sign Up</button>
            <p>Already have an account? <a href="login.php" class="login-link">Log In</a></p>
        </form>
    </div>
</div>

<script src="login.js"></script>
</body>
</html>