<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('location:login.php');
    exit();
}

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Update the user's status to inactive
    $update = "UPDATE users SET is_active = 0 WHERE user_id = :user_id";
    $stmt = $conn->prepare($update);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    header('location:admin_page.php');
    exit();
}
?>
