<?php
session_start();
include 'db.php';


if(!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin.php');
    exit();
}


$membersQuery = "SELECT COUNT(user_id) AS total_members FROM users";
$membersResult = $conn->query($membersQuery);
$totalMembers = $membersResult->fetch(PDO::FETCH_ASSOC)['total_members'];


$todayClassesQuery = "SELECT COUNT(class_id) AS classes_today FROM classes WHERE DATE(schedule_time) = CURDATE()";
$todayClassesResult = $conn->query($todayClassesQuery);
$classesToday = $todayClassesResult->fetch(PDO::FETCH_ASSOC)['classes_today'];


$trainersQuery = "SELECT COUNT(DISTINCT instructor_id) AS active_trainers FROM classes WHERE DATE(schedule_time) = CURDATE()";
$trainersResult = $conn->query($trainersQuery);
$activeTrainers = $trainersResult->fetch(PDO::FETCH_ASSOC)['active_trainers'];


$queriesQuery = "SELECT COUNT(query_id) AS pending_queries FROM queries WHERE status = 'pending'";
$queriesResult = $conn->query($queriesQuery);
$pendingQueries = $queriesResult->fetch(PDO::FETCH_ASSOC)['pending_queries'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FitZone Fitness Center</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-content">
        <?php include 'includes/header.php'; ?>
        <div class="dashboard">
            <br>
            <h1>Welcome, Admin</h1>
            <br>
            <div class="dashboard-metrics">
                <div class="metric">Total Members: <?php echo $totalMembers; ?></div>
                <div class="metric">Classes Today: <?php echo $classesToday; ?></div>
                <div class="metric">Active Trainers: <?php echo $activeTrainers; ?></div>
                <div class="metric">Pending Queries: <?php echo $pendingQueries; ?></div>
            </div>


            <h2>Today's Class Schedule</h2>
            <table>
                <tr>
                    <th>Class Name</th>
                    <th>Instructor</th>
                    <th>Time</th>
                    <th>Capacity</th>
                </tr>
                <?php

                $scheduleQuery = "
                    SELECT c.class_name, i.name AS instructor_name, c.schedule_time, c.capacity
                    FROM classes AS c
                    JOIN users AS i ON c.instructor_id = i.user_id
                    WHERE DATE(c.schedule_time) = CURDATE()";

                $scheduleResult = $conn->query($scheduleQuery);


                while($row = $scheduleResult->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>{$row['class_name']}</td>";
                    echo "<td>{$row['instructor_name']}</td>";
                    echo "<td>{$row['schedule_time']}</td>";
                    echo "<td>{$row['capacity']}</td>";
                    echo "</tr>";
                }


                if ($scheduleResult->rowCount() === 0) {
                    echo "<tr><td colspan='4'>No classes scheduled for today.</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>
</body>
</html>
