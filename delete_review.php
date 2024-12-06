<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('location:login.php');
    exit();
}

if (isset($_GET['id'])) {
    $review_id = $_GET['id'];

    // Delete review from the database
    $delete = "DELETE FROM reviews WHERE review_id = :review_id";
    $stmt = $conn->prepare($delete);
    $stmt->bindParam(':review_id', $review_id);
    $stmt->execute();

    header('location:admin_page.php');
    exit();
}
?>
