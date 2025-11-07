<?php
require_once 'config.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$search_results = [];
if (isset($_GET['query'])) {
    $query = '%' . $_GET['query'] . '%';
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username LIKE ? OR bio LIKE ?");
    $stmt->execute([$query, $query]);
    $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search - Social Media</title>
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

        <div class="search-container">
            <form method="GET" class="search-form">
                <input type="text" name="query" placeholder="Search users..." value="<?php echo $_GET['query'] ?? ''; ?>">
                <button type="submit">Search</button>
            </form>

            <div class="search-results">
                <?php foreach ($search_results as $user): ?>
                    <div class="user-result">
                        <img src="images/<?php echo $user['avatar']; ?>" alt="Avatar" class="avatar-small">
                        <div>
                            <h4><?php echo htmlspecialchars($user['username']); ?></h4>
                            <p><?php echo htmlspecialchars($user['bio']); ?></p>
                        </div>
                        <a href="follow.php?user_id=<?php echo $user['id']; ?>" class="btn-follow">Follow</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>