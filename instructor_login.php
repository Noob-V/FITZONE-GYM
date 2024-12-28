<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $identifier = $_POST['identifier']; 
    $password = $_POST['password'];

    
    $query = "SELECT * FROM instructors WHERE name = :identifier OR contact = :identifier"; 
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':identifier', $identifier);
    $stmt->execute();
    $instructor = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($instructor && password_verify($password, $instructor['password'])) {
        
        
        $_SESSION['instructor_logged_in'] = true;
        $_SESSION['instructor_id'] = $instructor['instructor_id'];
        $_SESSION['instructor_name'] = $instructor['name'];

        
        $userCheckStmt = $conn->prepare("SELECT user_id, role FROM users WHERE email = :email");
        $userCheckStmt->execute(['email' => $instructor['email']]);
        $userExists = $userCheckStmt->fetch(PDO::FETCH_ASSOC);

        if (!$userExists) {
            
            $insertUserStmt = $conn->prepare("INSERT INTO users (name, email, password, role, remembered_email, profile_picture) 
                                                VALUES (:name, :email, :password, 'staff', :remembered_email, :profile_picture)");
            $insertUserStmt->execute([
                'name' => $instructor['name'],
                'email' => $instructor['email'], 
                'password' => password_hash($password, PASSWORD_BCRYPT), 
                'remembered_email' => $instructor['email'], 
                'profile_picture' => 'default_profile.png' 
            ]);
            $userId = $conn->lastInsertId(); 
        } else {
            
            if ($userExists['role'] != 'staff') {
                $updateUserStmt = $conn->prepare("UPDATE users SET name = :name, role = 'staff' WHERE user_id = :user_id");
                $updateUserStmt->execute([
                    'name' => $instructor['name'],
                    'user_id' => $userExists['user_id']
                ]);
            }
            $userId = $userExists['user_id']; 
        }

        
        $insertLoginStmt = $conn->prepare("INSERT INTO login_records (user_id, status) VALUES (:user_id, 'successful')");
        $insertLoginStmt->execute(['user_id' => $userId]);

        
        header('Location: staff_dashboard.php');
        exit();
    } else {
        
        if ($instructor) {
            $userCheckStmt->execute(['email' => $instructor['email']]);
            $userExists = $userCheckStmt->fetch(PDO::FETCH_ASSOC);
            if ($userExists) {
                $insertLoginStmt = $conn->prepare("INSERT INTO login_records (user_id, status) VALUES (:user_id, 'failure')");
                $insertLoginStmt->execute(['user_id' => $userExists['user_id']]);
            }
        }
        
        
        echo "Invalid login credentials. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Login - FitZone Fitness Center</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="container">
        <h2>Instructor Login</h2>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <div class="input-field">
                <label for="identifier">Name or Email :</label>
                <input type="text" id="identifier" name="identifier" required> 
            </div>
            <div class="input-field">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
    </div>
</body>
</html>
