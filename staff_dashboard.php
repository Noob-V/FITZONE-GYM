<?php
session_start();
include 'db.php';

if (!isset($_SESSION['instructor_logged_in']) || $_SESSION['instructor_logged_in'] !== true) {
    header("Location: instructor_login.php");
    exit();
}

$stmt = $conn->prepare("SELECT class_id, class_name, schedule_time, capacity FROM classes WHERE instructor_id = :instructor_id");
$stmt->execute(['instructor_id' => $_SESSION['instructor_id']]);
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Dashboard - FitZone Fitness Center</title>
    <link rel="stylesheet" href="admin.css">

</head>

<body>

    <div class="sidebar">
        <h2>Instructor Dashboard</h2>
        <ul>
            <li><a href="attendance_tracking.php">Attendance Tracking</a></li>
            <li><a href="guides.html">Workout Guides</a></li>
            <li><a href="query_responses.php">Query Responses</a></li>
            <li><a href="blog.php">Blog</a></li>
            <li><a href="staff_profile.php">Profile Management</a></li>
            <li><a href="logout.php" class="logout-btn">Logout</a></li> 
        </ul>
    </div>


    <div class="main-content">
        <h2>Instructor Dashboard</h2>
        <br>

        <section id="class_schedule">
            <h3>Class Schedule Overview</h3>
            <br>

            <table>

                <thead>

                    <tr>
                        <th>Class Name</th>
                        <th>Schedule Time</th>
                        <th>Capacity</th>
                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($classes as $class): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($class['class_name']); ?></td>
                        <td><?php echo htmlspecialchars($class['schedule_time']); ?></td>
                        <td><?php echo htmlspecialchars($class['capacity']); ?></td>
                    </tr>
                    <?php endforeach; ?>

                </tbody>

            </table>

        </section>
        
    </div>

</body>

</html>
