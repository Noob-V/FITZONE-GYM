<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin.php');
    exit();
}


$query = "SELECT appointment_id, user_id, appointment_date, service_type, status FROM appointments";
$stmt = $conn->prepare($query);
$stmt->execute();
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointments - FitZone Admin</title>
    <link rel="stylesheet" href="admin_appointments.css">
</head>
<body>

    <?php include 'includes/sidebar.php'; ?>
    <div class="main-content">
        <?php include 'includes/header.php'; ?>
        <br>
        <h2>Manage Appointments</h2>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Appointment ID</th>
                    <th>User ID</th>
                    <th>Date</th>
                    <th>Service Type</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $appointment): ?>
                    <tr>
                        <td><?= htmlspecialchars($appointment['appointment_id']) ?></td>
                        <td><?= htmlspecialchars($appointment['user_id']) ?></td>
                        <td><?= htmlspecialchars($appointment['appointment_date']) ?></td>
                        <td><?= htmlspecialchars($appointment['service_type']) ?></td>
                        <td><?= htmlspecialchars($appointment['status']) ?></td>
                        <td>
                            <form method="post" action="update_appointment.php" style="display: inline;">
                                <input type="hidden" name="appointment_id" value="<?= $appointment['appointment_id'] ?>">
                                <button type="submit" name="action" value="approve" class="btn btn-approve">Approve</button>
                                <button type="submit" name="action" value="reject" class="btn btn-reject">Reject</button>
                            </form>
                            <a href="delete_appointment.php?appointment_id=<?= $appointment['appointment_id'] ?>" onclick="return confirm('Are you sure you want to delete this appointment?');" class="btn btn-delete">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
