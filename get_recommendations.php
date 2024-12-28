<?php
include 'db.php'; 

if (isset($_GET['service_id'])) {
    $service_id = $_GET['service_id'];

    $stmt = $conn->prepare("SELECT * FROM services WHERE service_id = :service_id");
    $stmt->bindParam(':service_id', $service_id, PDO::PARAM_INT);
    $stmt->execute();
    $recommendations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($recommendations);
}
?>
