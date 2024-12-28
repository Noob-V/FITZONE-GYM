<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['user_id'])) {
    $userId = $_GET['user_id']; 

    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = :user_id");

    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "User deleted successfully.";
        header("Location: manage_users.php"); 
        exit();
    } else {
        $errorInfo = $stmt->errorInfo();
        echo "Error deleting user: " . $errorInfo[2];
    }
    
} else {
    echo "Invalid request.";
}
