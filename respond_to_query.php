<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['query_id'], $_POST['response_text'])) {
    $query_id = $_POST['query_id'];
    $response_text = $_POST['response_text'];

    $stmt = $conn->prepare("UPDATE queries 
                            SET respond = :response_text, status = 'Responded' 
                            WHERE query_id = :query_id");
    $stmt->bindParam(':query_id', $query_id, PDO::PARAM_INT);
    $stmt->bindParam(':response_text', $response_text, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Response sent successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error responding to query.']);
    }
    exit();
}
?>
