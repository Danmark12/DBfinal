<?php
@include 'config.php';
session_start();

// Check if the user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header('location:admin_login.php');
    exit();
}

// Fetch all books
$books = $conn->query("SELECT * FROM books");

// Handle adding a new book
if (isset($_POST['add_book'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $cover_image = $_POST['cover_image'] ?: 'placeholder.jpg'; // Default cover image if not provided
    $review_count = 0; // Default review count is 0

    // Insert new book into the database
    $stmt = $conn->prepare("INSERT INTO books (title, author, description, category, cover_image, review_count) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $author, $description, $category, $cover_image, $review_count]);

    $success = "Book added successfully!";
}

// Handle deleting a book
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Delete the book from the database
    $stmt = $conn->prepare("DELETE FROM books WHERE book_id = ?");
    $stmt->execute([$delete_id]);

    $success = "Book deleted successfully!";
    header('Location: manage_books.php'); // Refresh page to show updated list
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books</title>
    <style>
        /* Inline CSS for this page */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h1 {
            text-align: center;
            font-size: 2em;
            margin-bottom: 40px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px 12px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
        .button {
            padding: 8px 16px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Manage Books</h1>

        <!-- Add New Book Form -->
        <form action="manage_books.php" method="POST">
            <input type="text" name="title" placeholder="Book Title" required><br><br>
            <input type="text" name="author" placeholder="Author" required><br><br>
            <textarea name="description" placeholder="Description" required></textarea><br><br>
            <input type="text" name="category" placeholder="Category"><br><br>
            <input type="text" name="cover_image" placeholder="Cover Image URL (optional)"><br><br>
            <button type="submit" name="add_book" class="button">Add Book</button>
        </form>

        <?php if (isset($success)) { echo '<p>' . $success . '</p>'; } ?>

        <!-- Books List -->
        <table>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Description</th>
                <th>Category</th>
                <th>Cover Image</th>
                <th>Reviews</th>
                <th>Actions</th>
            </tr>
            <?php while ($book = $books->fetch(PDO::FETCH_ASSOC)) { ?>
                <tr>
                    <td><?= $book['book_id'] ?></td>
                    <td><?= htmlspecialchars($book['title']) ?></td>
                    <td><?= htmlspecialchars($book['author']) ?></td>
                    <td><?= htmlspecialchars($book['description']) ?></td>
                    <td><?= htmlspecialchars($book['category']) ?></td>
                    <td><img src="<?= htmlspecialchars($book['cover_image']) ?>" alt="Cover Image" width="50"></td>
                    <td><?= $book['review_count'] ?></td>
                    <td>
                        <a href="manage_books.php?delete_id=<?= $book['book_id'] ?>" class="button" onclick="return confirm('Are you sure you want to delete this book?')">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>

        <br>
        <a href="admin_page.php" class="button">Back to Admin Page</a>
    </div>

</body>
</html>
