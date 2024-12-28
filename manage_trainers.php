<?php
session_start();
include 'db.php';

// Ensure the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin.php');
    exit();
}

$message = '';
$instructor_id = null;
$name = '';
$email = '';  // Added email field
$specialty = '';
$contact = '';
$password = '';

// Handle form submission for adding/updating instructor
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];  // Get email from the form
    $specialty = $_POST['specialty'];
    $contact = $_POST['contact'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    $instructor_id = $_POST['instructor_id'] ?? null; 

    if ($instructor_id) {
        // Update instructor with email
        $query = "UPDATE instructors SET name = :name, email = :email, specialty = :specialty, contact = :contact" . 
            (!empty($_POST['password']) ? ", password = :password" : "") . " WHERE instructor_id = :instructor_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':instructor_id', $instructor_id);
    } else {
        // Insert new instructor with email
        $query = "INSERT INTO instructors (name, email, specialty, contact, password) VALUES (:name, :email, :specialty, :contact, :password)";
        $stmt = $conn->prepare($query);
    }

    // Bind parameters to prevent SQL injection
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);  // Bind email to query
    $stmt->bindParam(':specialty', $specialty);
    $stmt->bindParam(':contact', $contact);

    if (!empty($_POST['password'])) { 
        $stmt->bindParam(':password', $password);
    }

    if ($stmt->execute()) {
        // Reset the form after successful submission
        $instructor_id = null; 
        $name = '';
        $email = '';  // Reset email field
        $specialty = '';
        $contact = '';
        $password = '';
        $message = $instructor_id ? 'Instructor updated successfully!' : 'Instructor added successfully!';
    } else {
        $message = 'Error saving instructor.';
    }
}

// Handle instructor deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_query = "DELETE FROM instructors WHERE instructor_id = :instructor_id";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bindParam(':instructor_id', $delete_id);
    
    if ($delete_stmt->execute()) {
        $message = 'Instructor deleted successfully!';
    } else {
        $message = 'Error deleting instructor.';
    }

    // Redirect after deletion
    header('Location: manage_trainers.php');
    exit();
}

// Fetch all instructors from the database
$instructors_query = "SELECT * FROM instructors";
$instructors_stmt = $conn->prepare($instructors_query);
$instructors_stmt->execute();
$instructors = $instructors_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch data for editing an instructor
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $edit_query = "SELECT * FROM instructors WHERE instructor_id = :instructor_id";
    $edit_stmt = $conn->prepare($edit_query);
    $edit_stmt->bindParam(':instructor_id', $edit_id);
    $edit_stmt->execute();
    $instructor_to_edit = $edit_stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($instructor_to_edit) {
        $instructor_id = $instructor_to_edit['instructor_id'];
        $name = $instructor_to_edit['name'];
        $email = $instructor_to_edit['email'];  // Set email value
        $specialty = $instructor_to_edit['specialty'];
        $contact = $instructor_to_edit['contact'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Instructors - FitZone Admin</title>
    <link rel="stylesheet" href="mi.css">
</head>
<body>

    <?php include 'includes/sidebar.php'; ?>
    <div class="main-content">
        <?php include 'includes/header.php'; ?>  
        <?php if ($message): ?>
            <p><?= $message ?></p>
        <?php endif; ?>

        <h2>Manage Instructors</h2>

        <form action="manage_trainers.php" method="POST">
            <input type="hidden" name="instructor_id" value="<?= $instructor_id ?>">
            
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($name) ?>" required>

            <label for="email">Email:</label>  <!-- New email input field -->
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($email) ?>" required>

            <label for="specialty">Specialty:</label>
            <input type="text" name="specialty" id="specialty" value="<?= htmlspecialchars($specialty) ?>" required>

            <label for="contact">Contact:</label>
            <input type="text" name="contact" id="contact" value="<?= htmlspecialchars($contact) ?>" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>

            <button type="submit" class="btn btn-primary"><?= isset($instructor_id) ? 'Update Instructor' : 'Add Instructor' ?></button>
        </form>

        <h3>Existing Instructors</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>  <!-- Display email column -->
                    <th>Specialty</th>
                    <th>Contact</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($instructors as $instructor): ?>
                    <tr>
                        <td><?= $instructor['instructor_id'] ?></td>
                        <td><?= htmlspecialchars($instructor['name']) ?></td>
                        <td><?= htmlspecialchars($instructor['email']) ?></td>  <!-- Display email value -->
                        <td><?= htmlspecialchars($instructor['specialty']) ?></td>
                        <td><?= htmlspecialchars($instructor['contact']) ?></td>
                        <td>
                            <a href="manage_trainers.php?edit_id=<?= $instructor['instructor_id'] ?>" class="table-action-btn edit">Edit</a> |
                            <a href="manage_trainers.php?delete_id=<?= $instructor['instructor_id'] ?>" class="table-action-btn delete" onclick="return confirm('Are you sure you want to delete this instructor?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>

</body>
</html>
