<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_POST['user_id'];  
    $name = $_POST['name'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("UPDATE users SET name = :name, email = :email WHERE user_id = :user_id");

    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "User updated successfully.";
    } else {
        
        $errorInfo = $stmt->errorInfo();
        echo "Error updating user: " . $errorInfo[2];
    }
}
?>
