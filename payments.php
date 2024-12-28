<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
include 'db.php';  // Use PDO connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Payments</title>
    <link rel="stylesheet" href="payments_admin.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="main-container">
        <?php include 'includes/sidebar.php'; ?>
        <div class="content">
            <h2>Manage Payments</h2>
            <div class="search-bar">
                <form method="GET" action="">
                    <input type="text" name="search" placeholder="Search payments (User ID, Membership, Status)" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit">Search</button>
                </form>
            </div>
            <div class="payment-table">
                <table>
                    <thead>
                        <tr>
                            <th>Payment ID</th>
                            <th>User ID</th>
                            <th>Membership ID</th>
                            <th>Amount</th>
                            <th>Payment Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $search = isset($_GET['search']) ? $_GET['search'] : '';
                        $query = "SELECT * FROM payments";
                        if ($search) {
                            $query .= " WHERE user_id LIKE :search OR membership_id LIKE :search OR status LIKE :search";
                        }

                        $stmt = $conn->prepare($query);
                        if ($search) {
                            $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
                        }
                        $stmt->execute();

                        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        if ($results) {
                            foreach ($results as $row) {
                                echo "<tr>
                                    <td>{$row['payment_id']}</td>
                                    <td>{$row['user_id']}</td>
                                    <td>{$row['membership_id']}</td>
                                    <td>{$row['amount']}</td>
                                    <td>{$row['payment_date']}</td>
                                    <td>{$row['status']}</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No payments found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
