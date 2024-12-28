<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php'; 
$user_id = $_SESSION['user_id'];

$query = "SELECT name FROM users WHERE user_id = :user_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$queries_query = $conn->prepare("SELECT query_id, query_text, response FROM queries WHERE user_id = :user_id");
$queries_query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$queries_query->execute();
$queries = $queries_query->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['query_text'])) {
    $query_text = $_POST['query_text'];

    $insert_query = $conn->prepare("INSERT INTO queries (user_id, query_text, status) VALUES (:user_id, :query_text, 'pending')");
    $insert_query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $insert_query->bindParam(':query_text', $query_text);
    $insert_query->execute();

    header("Location: query_submissionUser.php");
    exit();
}
?>

<!DOCTYPE html>

<html lang="en">
    
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="qs.css"> 
    <title>Query Submission - FitZone Fitness Center</title>

</head>

<body>

<header class="header">

    <h1>FitZone Dashboard</h1>

    <nav>
        <a href="index.html#home">Home</a>
        <a href="index.html#about">About Us</a>
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
        <li><a href="query_submission.php">Query Submission</a></li>
        <li><a href="profile_settings.php">Profile Settings</a></li>
    </ul>

</div>

<div class="main-content">

    <h2>Hello, <?php echo htmlspecialchars($user['name']); ?></h2>
    
    <h2>Submit a New Query</h2>

    <form method="POST" action="">
        <textarea class="query-textarea" name="query_text" placeholder="Send us a feedback or a Query....." required></textarea>
        <button type="submit">Submit Query</button>
    </form>

    <h2>Your Queries</h2>

    <?php if (!empty($queries)): ?>

        <table>

            <thead>

                <tr>
                    <th>Query ID</th>
                    <th>Your Query</th>
                    <th>Response</th>
                </tr>

            </thead>

            <tbody>

                <?php foreach ($queries as $query): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($query['query_id']); ?></td>
                        <td><?php echo htmlspecialchars($query['query_text']); ?></td>
                        <td>
                            <?php if (!empty($query['response'])): ?>
                                <?php echo htmlspecialchars($query['response']); ?>
                            <?php else: ?>
                                No response yet.
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

            </tbody>

        </table>

    <?php else: ?>
        <p>No queries found.</p>
    <?php endif; ?>

</div>

<footer>
    <p>&copy; 2024 FitZone Fitness Center. All rights reserved.</p>
</footer>

</body>

</html>
