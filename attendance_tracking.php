<?php
session_start();
include 'db.php';

if (!isset($_SESSION['instructor_logged_in']) || $_SESSION['instructor_logged_in'] !== true) {
    header("Location: instructor_login.php");
    exit();
}

$stmt = $conn->prepare("SELECT class_id, class_name FROM classes WHERE instructor_id = :instructor_id");
$stmt->execute(['instructor_id' => $_SESSION['instructor_id']]);
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mark_attendance'])) {
    $class_id = $_POST['class_id'];
    $attendance_data = explode(',', $_POST['attendance']); 

    foreach ($attendance_data as $participant_id) {
        $stmt = $conn->prepare("INSERT INTO attendance (class_id, participant_id, attendance_date) VALUES (:class_id, :participant_id, NOW())");
        $stmt->execute(['class_id' => $class_id, 'participant_id' => trim($participant_id)]);
    }
    header("Location: attendance_tracking.php?success=1");
    exit();
}

$attendance_stmt = $conn->prepare("
    SELECT c.class_name, a.participant_id, a.attendance_date 
    FROM attendance a 
    JOIN classes c ON a.class_id = c.class_id 
    WHERE c.instructor_id = :instructor_id 
    ORDER BY a.attendance_date DESC
");

$attendance_stmt->execute(['instructor_id' => $_SESSION['instructor_id']]);
$attendance_records = $attendance_stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Tracking - FitZone Fitness Center</title>
    <link rel="stylesheet" href="attendance_tracking.css">
</head>

<body>

    <div class="sidebar">

        <h2>Instructor Dashboard</h2>

        <ul>
            <li><a href="staff_dashboard.php">Dashboard</a></li>
            <li><a href="attendance_tracking.php">Attendance Tracking</a></li>
            <li><a href="guides.html">Workout Guides</a></li>
            <li><a href="query_responses.php">Query Responses</a></li>
            <li><a href="staff_profile.php">Profile Management</a></li>
            <li><a href="logout.php" class="logout-btn">Logout</a></li>
        </ul>

    </div>

    <div class="main-content">

        <h2>Attendance Tracking</h2>

        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">Attendance marked successfully!</div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">

            <label for="class_id">Select Class:</label>

            <select name="class_id" id="class_id" required>

                <option value="">Select Class</option>
                <?php foreach ($classes as $class): ?>
                    <option value="<?php echo $class['class_id']; ?>"><?php echo htmlspecialchars($class['class_name']); ?></option>
                <?php endforeach; ?>

            </select>
            <div>
                <label for="attendance">Mark Attendance for Participants (1,2,3....):</label>
                <input type="text" name="attendance" id="attendance" placeholder="e.g., 1,2,3" required>
            </div>

            <button type="submit" name="mark_attendance" class="btn">Mark Attendance</button>

        </form>

        <h3>Attendance Records</h3>

        <table>
            <thead>

                <tr>
                    <th>Class Name</th>
                    <th>Participant ID</th>
                    <th>Attendance Date</th>
                </tr>

            </thead>
            <tbody>

                <?php if (count($attendance_records) > 0): ?>
                    <?php foreach ($attendance_records as $record): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['class_name']); ?></td>
                        <td><?php echo htmlspecialchars($record['participant_id']); ?></td>
                        <td><?php echo htmlspecialchars($record['attendance_date']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">No attendance records found.</td>
                    </tr>
                <?php endif; ?>

            </tbody>

        </table>

    </div>

</body>

</html>
