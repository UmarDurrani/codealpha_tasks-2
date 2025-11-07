<?php
require_once 'config.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];
    $user_id = $_SESSION['user_id'];

    // Check if already liked
    $stmt = $pdo->prepare("SELECT id FROM likes WHERE user_id = ? AND post_id = ?");
    $stmt->execute([$user_id, $post_id]);
    
    if ($stmt->fetch()) {
        // Unlike
        $stmt = $pdo->prepare("DELETE FROM likes WHERE user_id = ? AND post_id = ?");
        $stmt->execute([$user_id, $post_id]);
    } else {
        // Like
        $stmt = $pdo->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $post_id]);
    }
}

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
exit;
?>