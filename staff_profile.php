<?php
session_start();
include('db.php'); 

if (!isset($_SESSION['instructor_id'])) {
    header('Location: instructor_login.php');
    exit();
}

$instructor_id = $_SESSION['instructor_id'];
$message = '';

$stmt = $conn->prepare("SELECT * FROM instructors WHERE instructor_id = :instructor_id");
$stmt->execute(['instructor_id' => $instructor_id]);
$instructor = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $specialty = $_POST['specialty'];
    $contact = $_POST['contact'];
    
    $updateStmt = $conn->prepare("UPDATE instructors SET name = :name, specialty = :specialty, contact = :contact WHERE instructor_id = :instructor_id");
    $updateStmt->execute([
        'name' => $name,
        'specialty' => $specialty,
        'contact' => $contact,
        'instructor_id' => $instructor_id
    ]);

    $message = 'Profile updated successfully!';
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Management - FitZone Fitness Center</title>
    <link rel="stylesheet" href="staff_profile.css"> 

</head>

<body>

    <div class="navbar">

        <h2>Instructor Dashboard</h2>

        <ul>
            <li><a href="attendance_tracking.php">Attendance Tracking</a></li>
            <li><a href="staff_dashboard.php">Dashboard</a></li>
            <li><a href="query_responses.php">Query Responses</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>

    </div>

    <div class="main-content">

        <h2>Profile Management</h2>

        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($instructor['name']); ?>" required>

            <label for="specialty">Specialty:</label>
            <input type="text" id="specialty" name="specialty" value="<?php echo htmlspecialchars($instructor['specialty']); ?>" required>

            <label for="contact">Contact:</label>
            <input type="text" id="contact" name="contact" value="<?php echo htmlspecialchars($instructor['contact']); ?>" required>

            <button type="submit">Update Profile</button>

        </form>

    </div>

</body>

</html>
