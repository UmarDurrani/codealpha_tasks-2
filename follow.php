<?php
require_once 'config.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['user_id'])) {
    $following_id = $_GET['user_id'];
    $follower_id = $_SESSION['user_id'];

    // Check if already following
    $stmt = $pdo->prepare("SELECT id FROM followers WHERE follower_id = ? AND following_id = ?");
    $stmt->execute([$follower_id, $following_id]);
    
    if ($stmt->fetch()) {
        // Unfollow
        $stmt = $pdo->prepare("DELETE FROM followers WHERE follower_id = ? AND following_id = ?");
        $stmt->execute([$follower_id, $following_id]);
    } else {
        // Follow
        $stmt = $pdo->prepare("INSERT INTO followers (follower_id, following_id) VALUES (?, ?)");
        $stmt->execute([$follower_id, $following_id]);
    }
}

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
exit;
?>