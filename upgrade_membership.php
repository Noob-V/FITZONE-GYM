<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT um.*, m.plan_name, m.price, m.benefits 
            FROM user_memberships um 
            JOIN memberships m ON um.membership_id = m.membership_id 
            WHERE um.user_id = :user_id AND um.status = 'active'";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$current_membership = $stmt->fetch(PDO::FETCH_ASSOC);

$query = "SELECT * FROM memberships";
$stmt = $conn->prepare($query);
$stmt->execute();
$membership_plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_plan_id'])) {
    $new_plan_id = $_POST['new_plan_id'];

    $updateStmt = $conn->prepare("UPDATE user_memberships 
                                    SET membership_id = :membership_id, 
                                        start_date = NOW(), 
                                        end_date = DATE_ADD(NOW(), INTERVAL 1 MONTH) 
                                    WHERE user_id = :user_id AND status = 'active'");
    $updateStmt->bindParam(':membership_id', $new_plan_id, PDO::PARAM_INT);
    $updateStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    
    if ($updateStmt->execute()) {

        $query = "SELECT m.plan_name, m.price, m.benefits 
                    FROM user_memberships um 
                    JOIN memberships m ON um.membership_id = m.membership_id 
                    WHERE um.user_id = :user_id AND um.status = 'active'";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $current_membership = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'message' => 'Membership upgraded successfully.', 'current_membership' => $current_membership]);
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => 'Could not update the plan.']);
        exit();
    }
}
?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membership Overview</title>
    <link rel="stylesheet" href="mo.css"> 
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body>

    <header>
        <h1>FitZone Fitness Center</h1>
    </header>

    <div class="container">

        <div class="sidebar">
            <a href="customer_dashboard.php">Dashboard</a>
            <a href="membership_overview.php">Membership Overview</a>
            <a href="blog.php">Blog</a>
            <a href="logout.php">Log Out</a>

        </div>

        <div class="membership-overview">

            <h2>Membership Overview</h2>

            <div class="current-plan">

                <h3>Your Current Membership Plan:</h3>

                <?php if ($current_membership): ?>
                    <h2><span><?php echo htmlspecialchars($current_membership['plan_name']); ?></span></h2>
                    <p>Price: <span>LKR <?php echo htmlspecialchars($current_membership['price']); ?></span></p>

                    <ul>
                        <?php
                        $benefits = explode(',', $current_membership['benefits']);
                        foreach ($benefits as $benefit) {
                            echo "<li>" . htmlspecialchars(trim($benefit)) . "</li>";
                        }
                        ?>
                    </ul>

                <?php else: ?>
                    <p>No active membership found.</p>
                <?php endif; ?>

            </div>

            <h3>Available Membership Plans:</h3>

            <div class="membership-plans">
                <?php foreach ($membership_plans as $plan): ?>
                    <div class="box">
                        <h3><?php echo htmlspecialchars($plan['plan_name']); ?></h3>
                        <h2><span>LKR <?php echo htmlspecialchars($plan['price']); ?> per month</span></h2>
                        <ul>
                            <?php
                            $benefits = explode(',', $plan['benefits']);
                            foreach ($benefits as $benefit) {
                                echo "<li>" . htmlspecialchars(trim($benefit)) . "</li>";
                            }
                            ?>
                        </ul>
                        <form class="upgrade-form">
                            <input type="hidden" name="new_plan_id" value="<?php echo $plan['membership_id']; ?>">
                            <button type="button" class="upgrade-button">Upgrade to this Plan</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>

            <h3>Your Past Payments:</h3>

            <table class="past-payments">
                <tr>
                    <th>Payment Date</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
                <?php
                $query = "SELECT * FROM payments WHERE user_id = :user_id ORDER BY payment_date DESC";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->execute();
                $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($payments as $payment) {
                    echo "<tr>
                            <td>" . htmlspecialchars($payment['payment_date']) . "</td>
                            <td>LKR " . htmlspecialchars($payment['amount']) . "</td>
                            <td>" . htmlspecialchars($payment['status']) . "</td>
                            </tr>";
                }
                ?>

            </table>

        </div>

    </div>

    <script src="upgrade_plan.js"></script>

</body>

</html>
