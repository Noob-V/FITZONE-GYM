<?php
session_start(); 
include 'db.php'; 

$error_message = ''; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $fixedPassword = 'admin@12345';

    try {
        
        $stmt = $conn->prepare("SELECT admin_id, name FROM admins WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        $loginStatus = 'failure'; 
        $userIdToLog = null; 

        if ($admin) {
            
            if ($password === $fixedPassword) {
                $_SESSION['admin_logged_in'] = true; 
                $_SESSION['email'] = $email;
                $_SESSION['admin_id'] = $admin['admin_id']; 

                
                $userCheckStmt = $conn->prepare("SELECT user_id FROM users WHERE email = :email");
                $userCheckStmt->execute(['email' => $email]);
                $userExists = $userCheckStmt->fetch(PDO::FETCH_ASSOC);

                if (!$userExists) {
                    
                    $insertUserStmt = $conn->prepare("INSERT INTO users (name, email, password, role, remembered_email, profile_picture) 
                                                        VALUES (:name, :email, :password, 'admin', :remembered_email, :profile_picture)");
                    $insertUserStmt->execute([
                        'name' => $admin['name'],
                        'email' => $email,
                        'password' => password_hash($fixedPassword, PASSWORD_BCRYPT), 
                        'remembered_email' => $email, 
                        'profile_picture' => 'default_profile.png' 
                    ]);
                }

                $updateStmt = $conn->prepare("UPDATE admins SET last_login = NOW() WHERE admin_id = :admin_id");
                $updateStmt->execute(['admin_id' => $admin['admin_id']]);

                $loginStatus = 'success'; 
                $userIdToLog = $admin['admin_id']; 
                session_regenerate_id(true);

                header("Location: admin_dashboard.php"); 
                exit();
            }
        }

        
        $error_message = "Invalid login credentials."; 

        
        $insertLoginStmt = $conn->prepare("INSERT INTO login_records (user_id, status) VALUES (:user_id, :status)");

        
        $insertLoginStmt->execute([
            'user_id' => $userIdToLog ?: 0, 
            'status' => $loginStatus
        ]);

    } catch (PDOException $e) {
        
        $error_message = "Database error: " . $e->getMessage(); 
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="login.css"> 
</head>
<body>
    <div class="container">
        <h2>Admin Login</h2>

        <?php if (!empty($error_message)): ?>
            <script type="text/javascript">
                alert("<?php echo $error_message; ?>");
            </script>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <div class="input-field">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
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
