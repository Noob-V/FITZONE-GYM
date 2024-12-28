<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include("db.php"); 
$user_id = $_SESSION['user_id'];

try {
    $query = "SELECT class_id, class_name, instructor_id, schedule_time, capacity FROM classes";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching classes: " . htmlspecialchars($e->getMessage());
    exit();
}

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['class_id'])) {
    $class_id = $_POST['class_id'];

    try {
        $check_capacity_query = "SELECT COUNT(*) as count FROM user_classes WHERE class_id = :class_id";
        $check_stmt = $conn->prepare($check_capacity_query);
        $check_stmt->bindParam(':class_id', $class_id, PDO::PARAM_INT);
        $check_stmt->execute();
        $count = $check_stmt->fetchColumn();

        
        $capacity_query = "SELECT capacity FROM classes WHERE class_id = :class_id";
        $capacity_stmt = $conn->prepare($capacity_query);
        $capacity_stmt->bindParam(':class_id', $class_id, PDO::PARAM_INT);
        $capacity_stmt->execute();
        $capacity = $capacity_stmt->fetchColumn();

        if ($count < $capacity) {
            
            $booking_query = "INSERT INTO user_classes (user_id, class_id) VALUES (:user_id, :class_id)";
            $booking_stmt = $conn->prepare($booking_query);
            $booking_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $booking_stmt->bindParam(':class_id', $class_id, PDO::PARAM_INT);
            if ($booking_stmt->execute()) {
                $successMessage = "Successfully booked for the class!";
            } else {
                $errorMessage = "Error booking the class. Please try again.";
            }
        } else {
            $errorMessage = "Class is full.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Error processing booking: " . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="class_booking.css">
    <title>Class Booking</title>
</head>

<body>

<header class="header">

    <h1>Class Booking</h1>

    <nav>
        <a href="customer_dashboard.php">Dashboard</a>
        <a href="index.html">Home</a>
        <a href="about.html">About Us</a>
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

    <h2>Available Classes</h2>
    
    <?php if ($successMessage): ?>
        <p class="success"><?php echo htmlspecialchars($successMessage); ?></p>
    <?php endif; ?>
    <?php if ($errorMessage): ?>
        <p class="error"><?php echo htmlspecialchars($errorMessage); ?></p>
    <?php endif; ?>

    <table>

        <thead>

            <tr>
                <th>Class Name</th>
                <th>Instructor</th>
                <th>Schedule Time</th>
                <th>Capacity</th>
                <th>Action</th>
            </tr>

        </thead>

        <tbody>

            <?php foreach ($classes as $class): ?>
                <tr>
                    <td><?php echo htmlspecialchars($class['class_name']); ?></td>
                    <td><?php echo htmlspecialchars($class['instructor_id']);?></td>
                    <td><?php echo htmlspecialchars($class['schedule_time']); ?></td>
                    <td><?php echo htmlspecialchars($class['capacity']); ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="class_id" value="<?php echo $class['class_id']; ?>">
                            <button type="submit">Book</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>

        </tbody>

    </table>

</div>

</body>

</html>
