<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointmentId = $_POST['appointment_id'];
    $action = $_POST['action'];

    $newStatus = '';
    if ($action === 'approve') {
        $newStatus = 'Approved';
    } elseif ($action === 'reject') {
        $newStatus = 'Rejected';
    }

    $query = "UPDATE appointments SET status = :status WHERE appointment_id = :appointment_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':status', $newStatus);
    $stmt->bindParam(':appointment_id', $appointmentId);

    if ($stmt->execute()) {
        header('Location: admin_appointments.php?message=Appointment updated successfully');
    } else {
        header('Location: admin_appointments.php?message=Failed to update appointment');
    }
    exit();
}
?>
