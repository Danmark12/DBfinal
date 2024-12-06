<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('location:login.php');
    exit();
}

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Delete user from the database
    $delete = "DELETE FROM users WHERE user_id = :user_id";
    $stmt = $conn->prepare($delete);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    header('location:admin_page.php');
    exit();
}
?>
