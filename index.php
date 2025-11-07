<?php
require_once 'config.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$current_user = get_user_by_id($pdo, $user_id);

// Get posts from users that current user follows
$stmt = $pdo->prepare("
    SELECT p.*, u.username, u.avatar, 
           (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as like_count,
           (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND user_id = ?) as user_liked,
           (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count
    FROM posts p 
    JOIN users u ON p.user_id = u.id 
    WHERE p.user_id = ? OR p.user_id IN (
        SELECT following_id FROM followers WHERE follower_id = ?
    )
    ORDER BY p.created_at DESC
");
$stmt->execute([$user_id, $user_id, $user_id]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get suggested users to follow
$stmt = $pdo->prepare("
    SELECT u.*, 
           (SELECT COUNT(*) FROM followers WHERE following_id = u.id) as follower_count
    FROM users u 
    WHERE u.id != ? AND u.id NOT IN (
        SELECT following_id FROM followers WHERE follower_id = ?
    )
    ORDER BY follower_count DESC 
    LIMIT 5
");
$stmt->execute([$user_id, $user_id]);
$suggested_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Media - Home</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Social Media</h1>
            <nav>
                <a href="index.php">Home</a>
                <a href="profile.php">Profile</a>
                <a href="search.php">Search</a>
                <a href="logout.php">Logout</a>
            </nav>
        </header>

        <div class="main-content">
            <div class="sidebar">
                <div class="user-info">
                    <img src="images/<?php echo $current_user['avatar']; ?>" alt="Avatar" class="avatar">
                    <h3><?php echo htmlspecialchars($current_user['username']); ?></h3>
                    <p><?php echo htmlspecialchars($current_user['bio']); ?></p>
                </div>

                <div class="suggested-users">
                    <h4>Suggested Users</h4>
                    <?php foreach ($suggested_users as $user): ?>
                        <div class="suggested-user">
                            <span><?php echo htmlspecialchars($user['username']); ?></span>
                            <a href="follow.php?user_id=<?php echo $user['id']; ?>" class="btn-follow">Follow</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="feed">
                <div class="create-post">
                    <form action="post.php" method="POST">
                        <textarea name="content" placeholder="What's on your mind?" required></textarea>
                        <button type="submit">Post</button>
                    </form>
                </div>

                <?php foreach ($posts as $post): ?>
                    <div class="post">
                        <div class="post-header">
                            <img src="images/<?php echo $post['avatar']; ?>" alt="Avatar" class="avatar-small">
                            <div>
                                <strong><?php echo htmlspecialchars($post['username']); ?></strong>
                                <span class="post-time"><?php echo date('M j, Y g:i A', strtotime($post['created_at'])); ?></span>
                            </div>
                        </div>
                        
                        <div class="post-content">
                            <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                            <?php if ($post['image']): ?>
                                <img src="images/<?php echo $post['image']; ?>" alt="Post image" class="post-image">
                            <?php endif; ?>
                        </div>

                        <div class="post-actions">
                            <a href="like.php?post_id=<?php echo $post['id']; ?>" class="like-btn <?php echo $post['user_liked'] ? 'liked' : ''; ?>">
                                Like (<?php echo $post['like_count']; ?>)
                            </a>
                            <a href="#" class="comment-btn" onclick="toggleComments(<?php echo $post['id']; ?>)">
                                Comment (<?php echo $post['comment_count']; ?>)
                            </a>
                        </div>

                        <div class="comments-section" id="comments-<?php echo $post['id']; ?>" style="display: none;">
                            <div class="comments-list">
                                <?php
                                $comment_stmt = $pdo->prepare("
                                    SELECT c.*, u.username, u.avatar 
                                    FROM comments c 
                                    JOIN users u ON c.user_id = u.id 
                                    WHERE c.post_id = ? 
                                    ORDER BY c.created_at ASC
                                ");
                                $comment_stmt->execute([$post['id']]);
                                $comments = $comment_stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                foreach ($comments as $comment):
                                ?>
                                    <div class="comment">
                                        <img src="images/<?php echo $comment['avatar']; ?>" alt="Avatar" class="avatar-xs">
                                        <div>
                                            <strong><?php echo htmlspecialchars($comment['username']); ?></strong>
                                            <p><?php echo htmlspecialchars($comment['content']); ?></p>
                                            <small><?php echo date('M j, g:i A', strtotime($comment['created_at'])); ?></small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <form action="comment.php" method="POST" class="comment-form">
                                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                <input type="text" name="content" placeholder="Write a comment..." required>
                                <button type="submit">Post</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>