<?php
session_start();
include('db.php'); 

$response = ['user_type' => 'guest'];

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    
    $stmt = $conn->prepare("SELECT * FROM admins WHERE admin_id = :user_id");
    $stmt->execute(['user_id' => $userId]);
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        $response['user_type'] = 'admin';
    }
    
    $stmt = $conn->prepare("SELECT * FROM instructors WHERE instructor_id = :user_id");
    $stmt->execute(['user_id' => $userId]);
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        $response['user_type'] = 'instructor';
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $userId]);
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        $response['user_type'] = 'customer';
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
