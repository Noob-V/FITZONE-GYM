<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin.php');
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $profile_picture = '';
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
        $upload_dir = 'uploads/';
        $profile_picture = basename($_FILES['profile_picture']['name']);
        $target_file = $upload_dir . $profile_picture;
        

        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
            $profile_picture = htmlspecialchars($profile_picture);
        } else {
            $message = 'Error uploading profile picture.';
        }
    }

    $query = "INSERT INTO users (name, email, password, role, profile_picture) VALUES (:name, :email, :password, :role, :profile_picture)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':profile_picture', $profile_picture);

    if ($stmt->execute()) {
        $message = 'User added successfully!';
    } else {
        $message = 'Error adding user.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add User - FitZone Admin</title>
    <link rel="stylesheet" href="add.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-content">
        <?php include 'includes/header.php'; ?>  
        <?php if ($message): ?>
            <p><?= $message ?></p>
        <?php endif; ?>

        <h2>Add an User</h2>

        <form action="add_user.php" method="POST" enctype="multipart/form-data">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>

            <label for="role">Role:</label>
            <select name="role" id="role" required>
                <option value="member">Customer</option>
                <option value="trainer">Trainer</option>
                <option value="admin">Admin</option>
            </select>

            <label for="profile_picture">Profile Picture:</label>
            <input type="file" name="profile_picture" id="profile_picture" accept="image/*">

            <button type="submit" class="btn btn-primary">Add User</button>
        </form>
    </div>
</body>
</html>
