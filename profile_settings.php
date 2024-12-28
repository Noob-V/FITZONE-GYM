<?php
session_start();
include('db.php'); 

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($_FILES['profile_picture']['name']);

        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFile)) {
            
            $updateStmt = $conn->prepare("UPDATE users SET name = :name, email = :email, profile_picture = :profile_picture WHERE user_id = :user_id");
            $updateStmt->execute([
                'name' => $name,
                'email' => $email,
                'profile_picture' => $uploadFile,
                'user_id' => $user_id
            ]);
        }
    }

    if (!empty($password)) {
        $updateStmt = $conn->prepare("UPDATE users SET name = :name, email = :email, password = :password WHERE user_id = :user_id");
        $updateStmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT), 
            'user_id' => $user_id
        ]);
    } else {
        
        $updateStmt = $conn->prepare("UPDATE users SET name = :name, email = :email WHERE user_id = :user_id");
        $updateStmt->execute([
            'name' => $name,
            'email' => $email,
            'user_id' => $user_id
        ]);
    }

    $message = 'Profile updated successfully!';
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings - FitZone Fitness Center</title>
    <link rel="stylesheet" href="profile.css"> 

</head>

<body>

    <div class="navbar">

        <h2>User Dashboard</h2>

        <ul>
            <li><a href="class_booking.php">Class Booking</a></li>
            <li><a href="membership_overview.php">Membership Overview</a></li>
            <li><a href="guides.html">Progress Tracking</a></li>
            <li><a href="personal_training_requests.php">Personal Training Requests</a></li>
            <li><a href="blog.php">Blog Access</a></li>
            <li><a href="query_submission.php">Query Submission</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>

    </div>

    <div class="main-content">

        <h2>Profile Settings</h2>

        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Leave blank to keep current password">

            <label for="profile_picture">Profile Picture:</label>
            <input type="file" id="profile_picture" name="profile_picture" accept="image/*">

            <button type="submit">Update Profile</button>

        </form>

    </div>

</body>

</html>