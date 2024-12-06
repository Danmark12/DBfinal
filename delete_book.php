<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('location:login.php');
    exit();
}

if (isset($_GET['id'])) {
    $book_id = $_GET['id'];

    // Delete book from the database
    $delete = "DELETE FROM books WHERE book_id = :book_id";
    $stmt = $conn->prepare($delete);
    $stmt->bindParam(':book_id', $book_id);
    $stmt->execute();

    header('location:admin_page.php');
    exit();
}
?>
