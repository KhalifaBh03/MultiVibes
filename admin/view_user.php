<?php
session_start();
include("../connect.php");
// Vérification de l'ID de l'admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_GET['id'];

// Récupération des informations de l'utilisateur
$query = "SELECT first_name, last_name, username, 
            (SELECT COUNT(*) FROM post WHERE user_id = ?) AS total_posts, 
            (SELECT COUNT(*) FROM likes WHERE post_id IN (SELECT id FROM post WHERE user_id = ?)) AS total_likes, 
            (SELECT COUNT(*) FROM follows WHERE id_followee = ?) AS total_followers 
          FROM user WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("iiii", $userId, $userId, $userId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "User not found.";
    exit;
}

$user = $result->fetch_assoc();

// Récupération des posts de l'utilisateur
$postQuery = "SELECT id, title,created_at FROM post WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $db->prepare($postQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$postResult = $stmt->get_result();

// Vérification si l'utilisateur courant suit cet utilisateur
$isFollowingQuery = "SELECT 1 FROM follows WHERE id_follower = ? AND id_followee = ?";
$stmt = $db->prepare($isFollowingQuery);
$stmt->bind_param("ii", $_SESSION['user_id'], $userId);
$stmt->execute();
$isFollowingResult = $stmt->get_result();
$isFollowing = $isFollowingResult->num_rows > 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/6133de38b3.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Profile of <?php echo htmlspecialchars($user['username']); ?></title>
    <style>
        .post-list {
            margin-top: 30px;
        }

        .post-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: rgb(237, 237, 237);
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 10px;
            transition: box-shadow 0.3s ease;
        }

        .post-item:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            cursor: pointer;
        }

        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
        }

        .main-content {
            margin-left: 280px;
            padding: 20px;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: static;
                height: auto;
                width: 100% !important;
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <?php
        include('include/sidebar.php');
    ?>
    <div class="main-content">
    <div class="container mt-5">
        <!-- Carte du profil -->
        <div>
            <h2><?php echo htmlspecialchars($user['first_name'] . " " . $user['last_name']); ?></h2>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <div>
                <div>
                    <strong>Posts:</strong> <?php echo $user['total_posts']; ?>
                    <?php if ($user['total_posts'] > 5): ?>
                        <span class="badge bg-success">Active</span>
                    <?php else: ?>
                        <span class="badge bg-warning">Novice</span>
                    <?php endif; ?>
                </div>
                <div><strong>Likes:</strong> <?php echo $user['total_likes']; ?></div>
                <div><strong>Followers:</strong> <?php echo $user['total_followers']; ?></div>
            </div>
        </div>

        <!-- Liste des posts -->
        <div class="post-list">
            <h3>Posts by <?php echo htmlspecialchars($user['username']); ?></h3>
            <?php if ($postResult->num_rows > 0): ?>
                <ul class="list-group">
                    <?php while ($post = $postResult->fetch_assoc()): ?>
                        <li class="post-item">
                            <a href="view_post.php?id=<?php echo $post['id']; ?>" style="text-decoration: none; color: inherit;">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </a>
                            <span style="color:grey;">
                                <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
                            </span>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No posts yet.</p>
            <?php endif; ?>
        </div>
    </div>
    </div>
    <script>
        var successMessage = document.getElementById('success-message');
        if (successMessage) {
            setTimeout(function() {
                successMessage.style.display = 'none';
            }, 2000);
        }
    </script>
</body>

</html>