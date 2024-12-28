<?php
session_start();
include 'db.php';


if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin.php'); 
    exit();
}


if (isset($_GET['delete_post'])) {
    $postId = $_GET['delete_post'];
    $deletePostStmt = $conn->prepare("DELETE FROM posts WHERE post_id = :post_id");
    $deletePostStmt->execute(['post_id' => $postId]);

    
    $deleteCommentsStmt = $conn->prepare("DELETE FROM comments WHERE post_id = :post_id");
    $deleteCommentsStmt->execute(['post_id' => $postId]);
    header('Location: blog_management.php');
    exit();
}

if (isset($_GET['delete_comment'])) {
    $commentId = $_GET['delete_comment'];
    $deleteCommentStmt = $conn->prepare("DELETE FROM comments WHERE comment_id = :comment_id");
    $deleteCommentStmt->execute(['comment_id' => $commentId]);
    header('Location: blog_management.php');
    exit();
}


$postsStmt = $conn->query("SELECT * FROM posts");
$commentsStmt = $conn->query("SELECT * FROM comments");

$posts = $postsStmt->fetchAll(PDO::FETCH_ASSOC);
$comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Management - FitZone Admin</title>
    <link rel="stylesheet" href="classes.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-content">
        <?php include 'includes/header.php'; ?>

        <h2>Blog Management</h2>
        <br>
        <h3>Posts</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Post ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Published Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                <tr>
                    <td><?php echo $post['post_id']; ?></td>
                    <td><?php echo htmlspecialchars($post['title']); ?></td>
                    <td><?php echo htmlspecialchars($post['author_name']); ?></td>
                    <td><?php echo htmlspecialchars($post['published_date']); ?></td>
                    <td><?php echo htmlspecialchars($post['status']); ?></td>
                    <td>
                        <a href="?delete_post=<?php echo $post['post_id']; ?>" class="button btn-delete" onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
                    <br>
        <h3>Comments</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Comment ID</th>
                    <th>Post ID</th>
                    <th>User Name</th>
                    <th>Content</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comments as $comment): ?>
                <tr>
                    <td><?php echo $comment['comment_id']; ?></td>
                    <td><?php echo $comment['post_id']; ?></td>
                    <td><?php echo htmlspecialchars($comment['user_name']); ?></td>
                    <td><?php echo htmlspecialchars($comment['content']); ?></td>
                    <td><?php echo htmlspecialchars($comment['created_at']); ?></td>
                    <td>
                        <a href="?delete_comment=<?php echo $comment['comment_id']; ?>" class="button btn-delete" onclick="return confirm('Are you sure you want to delete this comment?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
