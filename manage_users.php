<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin.php');
    exit();
}

$query = "SELECT user_id, name, email, role, profile_picture FROM users";
$stmt = $conn->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <title>Manage Users - FitZone Admin</title>
    <link rel="stylesheet" href="usersad.css">

    <script>

        function editUser (userId) {
            document.getElementById('name-' + userId).style.display = 'none';
            document.getElementById('email-' + userId).style.display = 'none';
            document.getElementById('edit-name-' + userId).style.display = 'inline';
            document.getElementById('edit-email-' + userId).style.display = 'inline';
            document.getElementById('save-' + userId).style.display = 'inline';
            document.getElementById('edit-' + userId).style.display = 'none';
        }

        function saveUser (userId) {
            const name = document.getElementById('edit-name-' + userId).value;
            const email = document.getElementById('edit-email-' + userId).value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_user.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    location.reload();
                }
            };
            xhr.send('user_id=' + userId + '&name=' + encodeURIComponent(name) + '&email=' + encodeURIComponent(email));
        }
    </script>

</head>

<body>

    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">

        <?php include 'includes/header.php'; ?>

        <h2>Manage Users</h2>
        <button onclick="location.href='add_user.php'" class="btn btn-primary">Add New User</button>

        <table class="table">

            <thead>
                <tr>
                    <th>Profile Picture</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($users as $user): ?>

                    <tr>
                        <td><img src="<?= htmlspecialchars($user['profile_picture']) ?>" width="50" height="50" alt="Profile Picture"></td>
                        <td>
                            <span id="name-<?= $user['user_id'] ?>"><?= htmlspecialchars($user['name']) ?></span>
                            <input type="text" id="edit-name-<?= $user['user_id'] ?>" value="<?= htmlspecialchars($user['name']) ?>" style="display:none;">
                        </td>
                        <td>
                            <span id="email-<?= $user['user_id'] ?>"><?= htmlspecialchars($user['email']) ?></span>
                            <input type="email" id="edit-email-<?= $user['user_id'] ?>" value="<?= htmlspecialchars($user['email']) ?>" style="display:none;">
                        </td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                        <td>
                            <button id="edit-<?= $user['user_id'] ?>" onclick="editUser (<?= $user['user_id'] ?>)" class="btn btn-warning">Edit</button>
                            <button id="save-<?= $user['user_id'] ?>" onclick="saveUser (<?= $user['user_id'] ?>)" style="display:none;" class="btn btn-success">Save</button>
                            <a href="delete_user.php?user_id=<?= $user['user_id'] ?>" onclick="return confirm('Are you sure you want to delete this user?');" class="btn btn-danger">Delete</a>
                        </td>
                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</body>

</html>