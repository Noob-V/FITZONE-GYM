<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin.php');
    exit();
}

$message = '';


$query = "SELECT c.class_id, c.class_name, c.schedule_time, c.capacity, c.instructor_id, i.name as instructor_name 
            FROM classes c JOIN instructors i ON c.instructor_id = i.instructor_id";
$stmt = $conn->prepare($query);
$stmt->execute();
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);


$instructorsQuery = "SELECT instructor_id, name FROM instructors";
$instructorsStmt = $conn->prepare($instructorsQuery);
$instructorsStmt->execute();
$instructors = $instructorsStmt->fetchAll(PDO::FETCH_ASSOC);


if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    $deleteQuery = "DELETE FROM classes WHERE class_id = :class_id";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->bindParam(':class_id', $deleteId);
    
    if ($deleteStmt->execute()) {
        $message = 'Class deleted successfully!';
        header("Location: manage_classes.php");
        exit();
    } else {
        $message = 'Error deleting class.';
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $classId = $_POST['class_id'] ?? null;
    $className = $_POST['class_name'];
    $instructorId = $_POST['instructor_id'];
    $scheduleTime = $_POST['schedule_time'];
    $capacity = $_POST['capacity'];

    if ($classId) {

        $updateQuery = "UPDATE classes SET class_name = :class_name, instructor_id = :instructor_id, 
                        schedule_time = :schedule_time, capacity = :capacity WHERE class_id = :class_id";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bindParam(':class_id', $classId);
    } else {

        $insertQuery = "INSERT INTO classes (class_name, instructor_id, schedule_time, capacity) VALUES 
                        (:class_name, :instructor_id, :schedule_time, :capacity)";
        $insertStmt = $conn->prepare($insertQuery);
    }


    $stmtToExecute = $classId ? $updateStmt : $insertStmt;
    $stmtToExecute->bindParam(':class_name', $className);
    $stmtToExecute->bindParam(':instructor_id', $instructorId);
    $stmtToExecute->bindParam(':schedule_time', $scheduleTime);
    $stmtToExecute->bindParam(':capacity', $capacity);

    if ($stmtToExecute->execute()) {
        $message = $classId ? 'Class updated successfully!' : 'Class added successfully!';
        header("Location: manage_classes.php");
        exit();
    } else {
        $message = 'Error adding/updating class.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Classes - FitZone Admin</title>
    <link rel="stylesheet" href="classes.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-content">
        <?php include 'includes/header.php'; ?>
        <?php if ($message): ?>
            <p><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <h2>Manage Classes</h2>
        <form action="manage_classes.php" method="POST">
            <input type="hidden" name="class_id" id="class_id" value="">
            <label for="class_name">Class Name:</label>
            <input type="text" name="class_name" id="class_name" required>
            
            <label for="instructor_id">Instructor:</label>
            <select name="instructor_id" id="instructor_id" required>
                <option value="">Select Instructor</option>
                <?php foreach ($instructors as $instructor): ?>
                    <option value="<?= $instructor['instructor_id'] ?>"><?= htmlspecialchars($instructor['name']) ?></option>
                <?php endforeach; ?>
            </select>
            
            <label for="schedule_time">Schedule Time:</label>
            <input type="datetime-local" name="schedule_time" id="schedule_time" required>
            
            <label for="capacity">Capacity:</label>
            <input type="number" name="capacity" id="capacity" required>
            
            <button type="submit" class="button btn-add-update">Add/Update Class</button>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>Class Name</th>
                    <th>Instructor</th>
                    <th>Schedule Time</th>
                    <th>Capacity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($classes as $class): ?>
                    <tr>
                        <td><?= htmlspecialchars($class['class_name']) ?></td>
                        <td><?= htmlspecialchars($class['instructor_name']) ?></td>
                        <td><?= htmlspecialchars($class['schedule_time']) ?></td>
                        <td><?= htmlspecialchars($class['capacity']) ?></td>
                        <td>
                            <button class="button btn-edit" onclick="editClass(<?= $class['class_id'] ?>, '<?= htmlspecialchars($class['class_name']) ?>', <?= $class['instructor_id'] ?>, '<?= $class['schedule_time'] ?>', <?= $class['capacity'] ?>)">Edit</button>
                            <a href="manage_classes.php?delete_id=<?= $class['class_id'] ?>" class="button btn-delete" onclick="return confirm('Are you sure you want to delete this class?');">Delete</a>
                        </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        function editClass(id, name, instructorId, scheduleTime, capacity) {
            document.getElementById('class_id').value = id;
            document.getElementById('class_name').value = name;
            document.getElementById('instructor_id').value = instructorId;
            document.getElementById('schedule_time').value = scheduleTime;
            document.getElementById('capacity').value = capacity;
        }
    </script>
</body>
</html>
