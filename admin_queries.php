<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}


$query = "SELECT q.*, u.name 
            FROM queries q 
            JOIN users u ON q.user_id = u.user_id"; 
$stmt = $conn->prepare($query);
$stmt->execute();
$queries = $stmt->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['query_id'], $_POST['response_text'])) {
    $query_id = $_POST['query_id'];
    $response_text = $_POST['response_text'];
    $status = 'Responded';

    $updateStmt = $conn->prepare("UPDATE queries 
                                SET response = :response, status = :status 
                                WHERE query_id = :query_id");
    $updateStmt->bindParam(':response', $response_text, PDO::PARAM_STR);
    $updateStmt->bindParam(':status', $status, PDO::PARAM_STR);
    $updateStmt->bindParam(':query_id', $query_id, PDO::PARAM_INT);

    if ($updateStmt->execute()) {
        // echo "<p>Response sent successfully.</p>";
    } else {
        echo "<p>Error sending response.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Customer Queries</title>
    <link rel="stylesheet" href="query.css">
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="admin_queries.php">Customer Queries</a></li>
            <li><a href="manage_users.php">Manage Users</a></li>
            <li><a href="logout.php" class="logout-btn">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <header class="header">
            <h2>Customer Queries</h2>
            <div class="header-right">
                <span>Welcome, Admin!</span>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </header>

        <div class="container">
            <table>
                <thead>
                    <tr>
                        <th>Query ID</th>
                        <th>Name</th>
                        <th>Query</th>
                        <th>Response</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($queries as $query): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($query['query_id']); ?></td>
                            <td><?php echo htmlspecialchars($query['name']); ?></td>
                            <td><?php echo htmlspecialchars($query['query_text']); ?></td>
                            <td><?php echo htmlspecialchars($query['response']); ?></td>
                            <td><?php echo htmlspecialchars($query['status']); ?></td>
                            <td>
                                <?php if ($query['status'] != 'Responded'): ?>
                                    <form method="POST">
                                        <input type="hidden" name="query_id" value="<?php echo $query['query_id']; ?>">
                                        <textarea name="response_text" required></textarea>
                                        <button type="submit" class="btn btn-primary">Send Response</button>
                                    </form>
                                <?php else: ?>
                                    <p>Response Sent</p>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
