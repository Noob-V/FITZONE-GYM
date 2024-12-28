<?php
session_start();
include 'db.php'; 

if (!isset($_SESSION['instructor_id'])) {
    header("Location: instructor_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['respond'])) {
    $query_id = $_POST['query_id'];
    $response = $_POST['response'];

    $sql = "UPDATE queries SET response = ?, status = 'responded' WHERE query_id = ?";
    
    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute([$response, $query_id]);
    } catch (PDOException $e) {
        echo "Error updating query: " . $e->getMessage();
    }
}

$sql = "SELECT q.query_id, q.user_id, q.query_text, q.response, q.status, u.name 
        FROM queries q 
        JOIN users u ON q.user_id = u.user_id 
        WHERE q.status = 'pending'"; 

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $queries = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching queries: " . $e->getMessage();
}
?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Query Responses - FitZone Fitness Center</title>
    <link rel="stylesheet" href="qr_staff.css"> 

</head>

<body>

    <div class="navbar">
        <h2>Instructor Dashboard</h2>
        <ul>
            <li><a href="attendance_tracking.php">Attendance Tracking</a></li>
            <li><a href="staff_dashboard.php">Dashboard</a></li>
            <li><a href="staff_profile.php">Profile Management</a></li>
            <li><a href="logout.php" class="logout-btn">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h2>Query Responses</h2>
        <?php if ($queries): ?>
            <table>
                <thead>
                    <tr>
                        <th>Query ID</th>
                        <th>User</th>
                        <th>Query</th>
                        <th>Response</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($queries as $query): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($query['query_id']); ?></td>
                            <td><?php echo htmlspecialchars($query['name']); ?></td>
                            <td><?php echo htmlspecialchars($query['query_text']); ?></td>
                            <td>
                                <?php if ($query['response']): ?>
                                    <?php echo htmlspecialchars($query['response']); ?>
                                <?php else: ?>
                                    <form method="post" action="">
                                        <input type="hidden" name="query_id" value="<?php echo $query['query_id']; ?>">
                                        <textarea name="response" placeholder="Type your response here..." required></textarea>
                                        <button type="submit" name="respond">Respond</button>
                                    </form>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        <?php else: ?>
            <p>No pending queries.</p>
        <?php endif; ?>

    </div>

</body>

</html>
