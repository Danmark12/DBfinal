<?php
@include 'config.php'; // Include the database connection
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
    exit();
}

if (isset($_POST['submit_review'])) {
    $user_id = $_SESSION['user_id'];
    $book_id = $_POST['book_id'];
    $rating = $_POST['rating'];
    $review_text = trim($_POST['review_text']);

    if (!empty($book_id) && !empty($rating) && !empty($review_text)) {
        try {
            $stmt = $conn->prepare("
                INSERT INTO reviews (user_id, book_id, rating, review_text) 
                VALUES (:user_id, :book_id, :rating, :review_text)
            ");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':book_id', $book_id);
            $stmt->bindParam(':rating', $rating);
            $stmt->bindParam(':review_text', $review_text);
            $stmt->execute();
            header('location:index.php?success=Review submitted successfully!');
            exit();
        } catch (PDOException $e) {
            header('location:index.php?error=Failed to submit review: ' . $e->getMessage());
            exit();
        }
    } else {
        header('location:index.php?error=All fields are required.');
        exit();
    }
}
?>
