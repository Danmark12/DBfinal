<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('location:login.php');
    exit();
}

if (isset($_GET['id'])) {
    $post_id = $_GET['id'];

    // Delete post from the database
    $delete = "DELETE FROM posts WHERE post_id = :post_id";
    $stmt = $conn->prepare($delete);
    $stmt->bindParam(':post_id', $post_id);
    $stmt->execute();

    header('location:admin_page.php');
    exit();
}
?>
