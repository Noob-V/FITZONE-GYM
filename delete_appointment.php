<?php
include 'db.php';

if (isset($_GET['appointment_id'])) {
    $appointmentId = $_GET['appointment_id'];

    $query = "DELETE FROM appointments WHERE appointment_id = :appointment_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':appointment_id', $appointmentId);

    if ($stmt->execute()) {
        header('Location: admin_appointments.php?message=Appointment deleted successfully');
    } else {
        header('Location: admin_appointments.php?message=Failed to delete appointment');
    }
    exit();
}
?>
