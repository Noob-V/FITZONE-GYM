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


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'upgrade' && isset($_POST['membership_id'])) {
        $membership_id = $_POST['membership_id'];


        $updateStmt = $conn->prepare("UPDATE user_memberships 
                                        SET membership_id = :membership_id, 
                                            start_date = NOW(), 
                                            end_date = DATE_ADD(NOW(), INTERVAL 1 MONTH) 
                                        WHERE user_id = :user_id AND status = 'active'");
        $updateStmt->bindParam(':membership_id', $membership_id, PDO::PARAM_INT);
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

    if ($action === 'renew') {

        $renewStmt = $conn->prepare("UPDATE user_memberships 
                                        SET end_date = DATE_ADD(end_date, INTERVAL 1 MONTH) 
                                        WHERE user_id = :user_id AND status = 'active'");
        $renewStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        
        if ($renewStmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Membership renewed successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Could not renew the membership.']);
        }
        exit();
    }

    if ($action === 'cancel') {

        $cancelStmt = $conn->prepare("UPDATE user_memberships 
                                        SET status = 'cancelled' 
                                        WHERE user_id = :user_id AND status = 'active'");
        $cancelStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        
        if ($cancelStmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Membership cancelled successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Could not cancel the membership.']);
        }
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
    <script src="membership.js"></script>
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
                    
                    <button id="renew-button"onclick="window.location.href='process_payment.php';">Renew Membership</button>
                    <button id="cancel-button">Cancel Membership</button>
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
                        <button type="button" class="upgrade-button" data-membership-id="<?php echo $plan['membership_id']; ?>" onclick="window.location.href='process_payment.php';">Upgrade to this Plan</button>
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
                
                $payment_query = "SELECT * FROM payments WHERE user_id = :user_id ORDER BY payment_date DESC";
                $payment_stmt = $conn->prepare($payment_query);
                $payment_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $payment_stmt->execute();
                $payments = $payment_stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($payments as $payment) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($payment['payment_date']) . "</td>"; 
                    echo "<td>LKR " . htmlspecialchars($payment['amount']) . "</td>";
                    echo "<td>" . htmlspecialchars($payment['status']) . "</td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
    </div>
</body>
</html>
