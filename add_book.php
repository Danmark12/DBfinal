<?php
@include 'config.php'; // Include the configuration for the database connection
session_start();

// Check if the user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header('location:admin_login.php'); // Redirect to admin login page if not an admin
    exit();
}

// Handle adding a new book
if (isset($_POST['add_book'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $description = $_POST['description'];
    $category = $_POST['category'];

    // Insert the new book into the books table
    $stmt = $conn->prepare("INSERT INTO books (title, author, description, category) VALUES (:title, :author, :description, :category)");
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':author', $author);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':category', $category);

    if ($stmt->execute()) {
        $success = "Book added successfully!";
    } else {
        $error = "Failed to add the book.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book</title>
    <link rel="stylesheet" href="css/admin.css"> <!-- Custom CSS -->
</head>
<body>

    <!-- Add Book Layout -->
    <div class="dashboard-container">
        <h1>Add Book</h1>

        <!-- Display Success or Error Message -->
        <?php if (isset($success)) { echo '<p class="success-msg">' . htmlspecialchars($success) . '</p>'; } ?>
        <?php if (isset($error)) { echo '<p class="error-msg">' . htmlspecialchars($error) . '</p>'; } ?>

        <!-- Add Book Form -->
        <form action="" method="POST">
            <label for="title">Book Title:</label>
            <input type="text" id="title" name="title" required placeholder="Enter book title">

            <label for="author">Author:</label>
            <input type="text" id="author" name="author" required placeholder="Enter book author">

            <label for="description">Description:</label>
            <textarea id="description" name="description" placeholder="Enter book description"></textarea>

            <label for="category">Category:</label>
            <select id="category" name="category" required>
                <option value="Fiction">Fiction</option>
                <option value="Nonfiction">Nonfiction</option>
                <option value="Science Fiction">Science Fiction</option>
                <option value="Mystery">Mystery</option>
                <option value="Romance">Romance</option>
                <option value="Fantasy">Fantasy</option>
                <option value="Biography">Biography</option>
            </select>

            <button type="submit" name="add_book">Add Book</button>
        </form>

        <!-- Back to Dashboard -->
        <div class="back-link">
            <a href="admin_dashboard.php">Back to Dashboard</a>
        </div>
    </div>

</body>
</html>
