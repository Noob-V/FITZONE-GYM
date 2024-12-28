<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php'; 
$user_id = $_SESSION['user_id'];

$query = "SELECT name, email FROM users WHERE user_id = :user_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$appointments_query = $conn->prepare("SELECT * FROM appointments WHERE user_id = :user_id");
$appointments_query->execute(['user_id' => $user_id]);
$appointments = $appointments_query->fetchAll(PDO::FETCH_ASSOC);

$trainers_query = $conn->query("SELECT name, email FROM instructors");
$trainers = $trainers_query->fetchAll(PDO::FETCH_ASSOC);

$services_query = $conn->query("SELECT service_id, service_name FROM services");
$services = $services_query->fetchAll(PDO::FETCH_ASSOC);

$recommendations = [];
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['service_type'])) {
    $selected_service_id = $_POST['service_type'];

    $recommendations_query = $conn->prepare("SELECT name FROM instructors WHERE specialty = (SELECT service_name FROM services WHERE service_id = :service_id)");
    $recommendations_query->bindParam(':service_id', $selected_service_id, PDO::PARAM_INT);
    $recommendations_query->execute();

    $recommendations = $recommendations_query->fetchAll(PDO::FETCH_ASSOC);
    
    try {
        $appointment_date = date('Y-m-d H:i:s'); 
        $service_type = $selected_service_id; 
        $status = 'pending'; 

        $insert_query = $conn->prepare("INSERT INTO appointments (user_id, appointment_date, service_type, status) VALUES (:user_id, :appointment_date, :service_type, :status)");
        $insert_query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $insert_query->bindParam(':appointment_date', $appointment_date);
        $insert_query->bindParam(':service_type', $service_type);
        $insert_query->bindParam(':status', $status);
        $insert_query->execute();
        
        header("Location: personal_training_requests.php"); 
        exit();
    } catch (PDOException $e) {
        echo "Error inserting appointment: " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="ptr.css"> 
    <title>Personal Training Requests - FitZone Fitness Center</title>

</head>

<body>

    <header class="header">

        <h1>FitZone Dashboard</h1>

        <nav>
            <a href="index.html#home">Home</a>
            <a href="index.html#about">About Us</a>
            <a href="logout.php">Log Out</a>
        </nav>

    </header>

    <div class="sidebar">

        <h2>Menu</h2>

        <ul>
            <li><a href="class_booking.php">Class Booking</a></li>
            <li><a href="membership_overview.php">Membership Overview</a></li>
            <li><a href="guides.html">Progress Tracking</a></li>
            <li><a href="personal_training_requests.php">Personal Training Requests</a></li>
            <li><a href="blog.php">Blog Access</a></li>
            <li><a href="query_submissionUser.php">Query Submission</a></li>
            <li><a href="profile_settings.php">Profile Settings</a></li>
        </ul>

    </div>

    <div class="main-content">

        <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?></h2>

        <h2>Your Training Requests</h2>

        <table>
            <thead>
                <tr>
                    <th>Appointment ID</th>
                    <th>Appointment Date</th>
                    <th>Service Type</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>

                <?php if (!empty($appointments)): ?>
                    <?php foreach ($appointments as $appointment): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($appointment['appointment_id']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['service_type']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No training requests found.</td>
                    </tr>
                <?php endif; ?>

            </tbody>

        </table>

        <h2>Request a Personal Training Session</h2>

        <form method="POST" action="">

            <label for="service_type">Select Service Type:</label>
            <select id="service_type" name="service_type" required>
                <option value="">--Choose a service--</option>
                <?php foreach ($services as $service): ?>
                    <option value="<?php echo htmlspecialchars($service['service_id']); ?>"><?php echo htmlspecialchars($service['service_name']); ?></option>
                <?php endforeach; ?>

            </select>

            <button type="submit">Get Recommendations</button>

        </form>

        <?php if (!empty($recommendations)): ?>
            <h3>Recommended Trainers for Selected Service:</h3>
            <ul>
                <?php foreach ($recommendations as $recommendation): ?>
                    <li><?php echo htmlspecialchars($recommendation['name']); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <h2>Available Trainers</h2>

        <table>

            <thead>
                <tr>
                    <th>Trainer Name</th>
                    <th>Email</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($trainers as $trainer): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($trainer['name']); ?></td>
                        <td><?php echo htmlspecialchars($trainer['email']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>

    </div>

    <footer>
        <p>&copy; 2024 FitZone Fitness Center. All rights reserved.</p>
    </footer>

</body>

</html>
