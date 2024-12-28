<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];


$query = "SELECT * FROM memberships";
$stmt = $conn->prepare($query);
$stmt->execute();
$membership_plans = $stmt->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['membership_id'], $_POST['amount'])) {
    $membership_id = $_POST['membership_id'];
    $amount = $_POST['amount'];


    $paymentStmt = $conn->prepare("INSERT INTO payments (user_id, membership_id, amount, payment_date, status) 
                                    VALUES (:user_id, :membership_id, :amount, NOW(), 'completed')");
    $paymentStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $paymentStmt->bindParam(':membership_id', $membership_id, PDO::PARAM_INT);
    $paymentStmt->bindParam(':amount', $amount, PDO::PARAM_STR);

    if ($paymentStmt->execute()) {

        $deactivateStmt = $conn->prepare("UPDATE user_memberships 
                                            SET status = 'inactive' 
                                            WHERE user_id = :user_id AND status = 'active'");
        $deactivateStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $deactivateStmt->execute();


        $insertStmt = $conn->prepare("INSERT INTO user_memberships 
                                        (user_id, membership_id, start_date, end_date, status) 
                                        VALUES (:user_id, :membership_id, NOW(), DATE_ADD(NOW(), INTERVAL 1 MONTH), 'active')");
        $insertStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $insertStmt->bindParam(':membership_id', $membership_id, PDO::PARAM_INT);

        if ($insertStmt->execute()) {

            echo json_encode(['success' => true, 'message' => 'Membership updated successfully!']);
            exit();
        } else {

            echo json_encode(['success' => false, 'message' => 'Payment was successful, but adding new membership failed: ' . implode(" ", $insertStmt->errorInfo())]);
            exit();
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Payment failed: ' . implode(" ", $paymentStmt->errorInfo())]);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - FitZone Fitness Center</title>
    <link rel="stylesheet" href="payment.css"> 
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header>
        <h1>FitZone Fitness Center</h1>
    </header>
    <div class="container">
        <h2>Complete Your Payment</h2>
        <div id="payment-message"></div>
        <form id="payment-form" method="POST">
            <div class="form-group">
                <label for="membership_id">Select Membership Plan:</label>
                <select id="membership_id" name="membership_id" required>
                    <option value="">--Select a plan--</option>
                    <?php foreach ($membership_plans as $plan): ?>
                        <option value="<?php echo $plan['membership_id']; ?>">
                            <?php echo htmlspecialchars($plan['plan_name']); ?> - LKR <?php echo htmlspecialchars($plan['price']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="amount">Amount (LKR):</label>
                <input type="text" id="amount" name="amount" required>
            </div>
            <button type="submit">Pay Now</button>
        </form>
    </div>
    <script>
        $(document).ready(function() {
            $('#payment-form').on('submit', function(e) {
                e.preventDefault();
                
                var membershipId = $('#membership_id').val();
                var amount = $('#amount').val();
                
                if (membershipId === "") {
                    alert("Please select a membership plan.");
                    return;
                }

                $.ajax({
                    type: 'POST',
                    url: '', 
                    data: {
                        membership_id: membershipId,
                        amount: amount
                    },
                    dataType: 'json',  
                    success: function(response) {
                        if (response.success) {
                            $('#payment-message').text(response.message);
                            setTimeout(function() {
                                location.href = "membership_overview.php"; 
                            }, 2000);
                        } else {
                            $('#payment-message').text(response.message);
                        }
                    },
                    error: function() {
                        $('#payment-message').text('An error occurred while processing the payment.');
                    }
                });
            });
        });
    </script>
</body>
</html>
