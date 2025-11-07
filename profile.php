<?php
require_once 'config.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$current_user = get_user_by_id($pdo, $user_id);

// Get user's posts
$stmt = $pdo->prepare("
    SELECT p.*, 
           (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as like_count,
           (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count
    FROM posts p 
    WHERE p.user_id = ? 
    ORDER BY p.created_at DESC
");
$stmt->execute([$user_id]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get follower/following counts
$followers_count = $pdo->prepare("SELECT COUNT(*) FROM followers WHERE following_id = ?");
$followers_count->execute([$user_id]);
$followers = $followers_count->fetchColumn();

$following_count = $pdo->prepare("SELECT COUNT(*) FROM followers WHERE follower_id = ?");
$following_count->execute([$user_id]);
$following = $following_count->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Social Media</title>
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

        <div class="profile-header">
            <img src="images/<?php echo $current_user['avatar']; ?>" alt="Avatar" class="avatar-large">
            <div class="profile-info">
                <h2><?php echo htmlspecialchars($current_user['username']); ?></h2>
                <p class="bio"><?php echo htmlspecialchars($current_user['bio']); ?></p>
                <div class="stats">
                    <span><strong><?php echo count($posts); ?></strong> Posts</span>
                    <span><strong><?php echo $followers; ?></strong> Followers</span>
                    <span><strong><?php echo $following; ?></strong> Following</span>
                </div>
            </div>
        </div>

        <div class="profile-posts">
            <h3>Your Posts</h3>
            <?php foreach ($posts as $post): ?>
                <div class="post">
                    <div class="post-content">
                        <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                        <div class="post-stats">
                            <span><?php echo $post['like_count']; ?> Likes</span>
                            <span><?php echo $post['comment_count']; ?> Comments</span>
                            <span><?php echo date('M j, Y g:i A', strtotime($post['created_at'])); ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>