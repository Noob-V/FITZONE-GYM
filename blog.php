<?php
include('db.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);


$stmt = $conn->query("SELECT * FROM posts WHERE status = 'published' ORDER BY published_date DESC");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_post'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $authorName = 'Anonymous'; 
    $image = null;


    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        $imageName = basename($_FILES['image']['name']);
        $targetFilePath = $targetDir . $imageName;
        $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));


        $validFileTypes = ['jpg', 'png', 'jpeg', 'gif'];
        if (in_array($imageFileType, $validFileTypes) && $_FILES['image']['size'] < 5000000) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                $image = $imageName;
            } else {
                echo "Error uploading the image file.";
            }
        } else {
            echo "Invalid file type or file too large.";
        }
    }


    $stmt = $conn->prepare("INSERT INTO posts (title, content, image, status, published_date, author_name) VALUES (:title, :content, :image, 'published', NOW(), :author_name)");
    $stmt->execute(['title' => $title, 'content' => $content, 'image' => $image, 'author_name' => $authorName]);
    header("Location: blog.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $postId = $_POST['post_id'];
    $comment = trim($_POST['comment']);
    $commenterEmail = 'Anonymous'; 

    if (!empty($commenterEmail) && !empty($comment)) {
        $stmt = $conn->prepare("INSERT INTO comments (post_id, email, content, created_at, status) VALUES (:post_id, :email, :content, NOW(), 'approved')");
        $stmt->execute(['post_id' => $postId, 'email' => $commenterEmail, 'content' => $comment]);
        header("Location: blog.php");
        exit();
    } else {
        echo "Unable to post comment. Email or comment is not set.";
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post'])) {
    $postId = $_POST['post_id'];
    $stmt = $conn->prepare("DELETE FROM posts WHERE post_id = :post_id");
    $stmt->execute(['post_id' => $postId]);
    header("Location: blog.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_comment'])) {
    $commentId = $_POST['comment_id'];
    $stmt = $conn->prepare("DELETE FROM comments WHERE comment_id = :comment_id");
    $stmt->execute(['comment_id' => $commentId]);
    header("Location: blog.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog</title>
    <link rel="stylesheet" href="blog.css">
</head>
<body>
<div class="nav">
    <div class="logo"><b>Community Blogs</b></div>
    <div class="nav-links">
        <a href="index.html#home">Home</a>
        <a href="blogContent.html">Latest Blogs</a>
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php">Log Out</a>
    </div>
</div>

<h1>Blog Posts</h1>

<div id="createPostForm" class="create-post-form">
    <form method="POST" enctype="multipart/form-data">
        <h2>Create a New Post</h2>
        <input type="text" name="title" required placeholder="Post Title">
        <textarea name="content" required placeholder="Post Content"></textarea>
        <input type="file" name="image" accept="image/*">
        <button type="submit" name="create_post">Create Post</button>
    </form>
</div>

<?php foreach ($posts as $post): ?>
    <article class="post">
        <h2><?php echo htmlspecialchars($post['title']); ?></h2>
        <?php if ($post['image']): ?>
            <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" style="max-width: 100%; height: auto;">
        <?php endif; ?>
        <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
        <p><small>Published by: <?php echo htmlspecialchars($post['author_name']); ?> on <?php echo $post['published_date']; ?></small></p>

        <h3>Comments:</h3>
        <form method="POST">
            <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
            <textarea name="comment" required placeholder="Add a comment"></textarea>
            <button type="submit">Submit Comment</button>
        </form>

        <?php
        $stmt = $conn->prepare("SELECT * FROM comments WHERE post_id = :post_id AND status = 'approved'");
        $stmt->execute(['post_id' => $post['post_id']]);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($comments as $comment): ?>
            <div class="comment">
                <strong><?php echo htmlspecialchars($comment['email']); ?>:</strong>
                <div class="comment-content"><?php echo nl2br(htmlspecialchars($comment['content'])); ?></div>
                <form method="POST">
                    <input type="hidden" name="comment_id" value="<?php echo $comment['comment_id']; ?>">
                    <button type="submit" name="delete_comment">Delete Comment</button>
                </form>
            </div>
        <?php endforeach; ?>
    </article>
<?php endforeach; ?>

</body>
</html>
