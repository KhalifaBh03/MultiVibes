<?php
session_start();
include("../connect.php");

// Vérifier si un ID de post est fourni
if (!isset($_GET['id'])) {
    $_SESSION['message'] = "Invalid post ID.";
    $_SESSION['message_type'] = "danger";
    header("Location: all_posts.php");
    exit;
}

$post_id = $_GET['id'];

// Supprimer les likes associés au post
$query_likes = "DELETE FROM likes WHERE post_id = ?";
$stmt_likes = $db->prepare($query_likes);
$stmt_likes->bind_param("i", $post_id);
$stmt_likes->execute();
$stmt_likes->close();

// Supprimer les commentaires associés au post
$query_comments = "DELETE FROM comments WHERE post_id = ?";
$stmt_comments = $db->prepare($query_comments);
$stmt_comments->bind_param("i", $post_id);
$stmt_comments->execute();
$stmt_comments->close();

//supprimer les post
$query_post = "DELETE FROM post WHERE id = ?";
$stmt_post = $db->prepare($query_post);
$stmt_post->bind_param("i", $post_id);

if ($stmt_post->execute()) {
    $_SESSION['message'] = "Post deleted successfully.";
    $_SESSION['message_type'] = "success";
} else {
    $_SESSION['message'] = "Failed to delete the post.";
    $_SESSION['message_type'] = "danger";
}

$stmt_post->close();

header("Location: all_posts.php");
exit;
?>