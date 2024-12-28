<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include("db.php");
$user_id = $_SESSION['user_id'];


$query = "SELECT name, email FROM users WHERE user_id = :user_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="cd.css">
    <title>User Dashboard</title>
</head>
<body>

    <header class="header">
        <h1>FitZone Dashboard</h1>
        <nav>
            <a href="index.html#home">Home</a>
            <a href="index.html#about">About Us</a>
            <a href="https://docs.google.com/forms/d/e/1FAIpQLScOYgBH3wmPzB0wgKxUmb8OYZd1tQkJU8AewuYsWgVKeFFwCw/viewform?usp=sf_link">Feedback</a>
            <a href="logout.php">Log Out</a>
        </nav>
    </header>

    <div class="sidebar">
        <h2>Menu</h2>
        <ul>
            <li><a href="class_booking.php">Class Booking</a></li>
            <li><a href="membership_overview.php">Membership Overview</a></li>
            <li><a href="guides.html">Progress Tracking</a></li>
            <li><a href="personal_training_requests.php">Personal Training Requests</a></li>
            <li><a href="blog.php">Blog Access</a></li>
            <li><a href="query_submissionUser.php">Query Submission</a></li>
            <li><a href="profile_settings.php">Profile Settings</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h1>Welcome to Your Dashboard</h1>
        
        <div class="card">
            <h2>Class Booking</h2>
            <p>Browse and register for classes.</p>
        </div>

        <div class="card">
            <h2>Membership Overview</h2>
            <p>View your membership status and upgrade options.</p>
        </div>

        <div class="card">
            <h2>Progress Tracking</h2>
            <p>Monitor your fitness goals.</p>
        </div>
        
        <div class="card">
            <h2>Personal Training Requests</h2>
            <p>Request personal training sessions.</p>
        </div>

        <footer>
            <p>&copy; 2024 FitZone Fitness Center. All rights reserved.</p>
        </footer>
    </div>

</body>
</html>

