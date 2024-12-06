<?php
@include 'config.php'; // Include the configuration for the database connection
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('location:login.php'); // Redirect to login page if not logged in
    exit();
}

// Fetch logged-in user details
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE user_id = :user_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// If the user doesn't exist in the users table, logout the user
if (!$user) {
    session_destroy();
    header('location:login.php'); // Redirect to login page
    exit();
}

// Fetch posts with book details, likes, and comments, excluding the logged-in user's posts
$posts_query = "
    SELECT p.*, b.title AS book_title, b.author AS book_author, u.username AS post_author
    FROM posts p
    LEFT JOIN books b ON p.book_id = b.book_id
    JOIN users u ON p.user_id = u.user_id
    WHERE p.user_id != :user_id  -- Exclude posts by the logged-in user
    ORDER BY p.created_at DESC
";
$stmt = $conn->prepare($posts_query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all books for dropdown in post creation
$books = $conn->query("SELECT * FROM books ORDER BY title");

// Fetch all books for the Books section (grid view)
$books_list = $conn->query("SELECT * FROM books ORDER BY title")->fetchAll(PDO::FETCH_ASSOC);

// Handle new post submission
if (isset($_POST['create_post'])) {
    $book_id = isset($_POST['book_id']) ? $_POST['book_id'] : null;
    $content = trim($_POST['content']);  // Trim any excess spaces from the content field

    if (isset($user_id) && !empty($content)) {
        try {
            $stmt = $conn->prepare("INSERT INTO posts (user_id, book_id, content) VALUES (:user_id, :book_id, :content)");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':book_id', $book_id, PDO::PARAM_NULL); // Bind NULL for book_id if not selected
            $stmt->bindParam(':content', $content);

            if ($stmt->execute()) {
                $success = "Post created successfully!";
            } else {
                $error = "Failed to create the post.";
            }
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    } else {
        $error = "Content cannot be empty.";
    }
}

// Handle comment submission
if (isset($_POST['comment'])) {
    $post_id = $_POST['post_id'];
    $comment_content = trim($_POST['comment_content']);

    if (!empty($comment_content)) {
        try {
            $stmt = $conn->prepare("INSERT INTO comments (user_id, post_id, comment_text) VALUES (:user_id, :post_id, :comment_text)");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':post_id', $post_id);
            $stmt->bindParam(':comment_text', $comment_content);

            if ($stmt->execute()) {
                $success_comment = "Comment added successfully!";
            } else {
                $error_comment = "Failed to add comment.";
            }
        } catch (PDOException $e) {
            $error_comment = "Error: " . $e->getMessage();
        }
    } else {
        $error_comment = "Comment cannot be empty.";
    }
}

// Handle review submission
if (isset($_POST['submit_review'])) {
    $book_id = $_POST['book_id'];
    $review = trim($_POST['review']);
    $rating = $_POST['rating'];

    if (!empty($review) && isset($book_id) && isset($user_id) && !empty($rating)) {
        // Check if the user has already reviewed the book
        $check_review_query = "SELECT * FROM reviews WHERE user_id = :user_id AND book_id = :book_id";
        $check_stmt = $conn->prepare($check_review_query);
        $check_stmt->bindParam(':user_id', $user_id);
        $check_stmt->bindParam(':book_id', $book_id);
        $check_stmt->execute();
        $existing_review = $check_stmt->fetch(PDO::FETCH_ASSOC);

        if (!$existing_review) {
            try {
                // Insert the review into the database
                $stmt = $conn->prepare("INSERT INTO reviews (user_id, book_id, review, rating) VALUES (:user_id, :book_id, :review, :rating)");
                $stmt->bindParam(':user_id', $user_id);
                $stmt->bindParam(':book_id', $book_id);
                $stmt->bindParam(':review', $review);
                $stmt->bindParam(':rating', $rating);

                if ($stmt->execute()) {
                    // Do not set success or error messages in the floating review card
                    // You can set success in the general interface if needed.
                } else {
                    // Error in review insertion, no success message in the floating review card
                }
            } catch (PDOException $e) {
                // Error in review insertion, no success message in the floating review card
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Page</title>
    <link rel="stylesheet" href="css/user.css">
    <style>
        .review-form {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.3);
            width: 300px;
        }
        .review-form textarea {
            width: 100%;
            height: 100px;
        }
        .review-form select, .review-form textarea {
            margin-bottom: 10px;
        }
        .review-form button {
            width: 100%;
        }
        .close-review-form {
            position: absolute;
            top: 5px;
            right: 5px;
            font-size: 18px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Top Navigation Bar -->
        <div class="top-nav">
            <div class="search-bar">
                <input type="text" placeholder="Search..." id="searchInput">
            </div>
            <div class="nav-icons">
                <div class="profile-dropdown">
                    <img src="<?= $user['profile_picture'] ?: 'default-profile.png' ?>" alt="Profile Picture" id="profilePicture">
                    <div class="dropdown-content" id="profileDropdownMenu">
                        <a href="view_profile.php">View Profile</a>
                        <a href="feedback.php">Feedback</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </div>
                <div class="message-dropdown">
                    <span id="messageIcon">💬</span>
                    <div class="dropdown-content" id="messageDropdownMenu">
                        <a href="messages.php">View Messages</a>
                        <a href="new_message.php">New Message</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="main-container">
            <div class="side-nav">
                <button class="tab-button" onclick="switchTab('home')">Home</button>
                <button class="tab-button" onclick="switchTab('books')">Books</button>
                <button class="tab-button" onclick="switchTab('friends')">Friends</button>
            </div>

            <div class="main-content">
                <div id="home" class="tab-content active">
                    <h3>Home</h3>
                    <div class="create-post-form">
                        <form method="POST" action="">
                            <select name="book_id">
                                <option value="">No Book (Optional)</option>
                                <?php foreach ($books as $book): ?>
                                    <option value="<?= $book['book_id'] ?>"><?= htmlspecialchars($book['title']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <textarea name="content" placeholder="What's on your mind?" required></textarea>
                            <button type="submit" name="create_post">Create Post</button>
                        </form>
                    </div>
                    <div class="posts">
                        <?php foreach ($posts as $post): ?>
                            <div class="post">
                                <p><strong><?= htmlspecialchars($post['post_author']) ?></strong> - <?= htmlspecialchars($post['created_at']) ?></p>
                                <p><?= htmlspecialchars($post['content']) ?></p>
                                <p>Book: <?= htmlspecialchars($post['book_title']) ?> by <?= htmlspecialchars($post['book_author']) ?></p>
                                <div class="comments">
                                    <form method="POST" action="">
                                        <input type="hidden" name="post_id" value="<?= $post['post_id'] ?>">
                                        <textarea name="comment_content" placeholder="Write a comment..." required></textarea>
                                        <button type="submit" name="comment">Comment</button>
                                    </form>
                                    <?php
                                    $comments_query = "SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id = u.user_id WHERE c.post_id = :post_id";
                                    $comments_stmt = $conn->prepare($comments_query);
                                    $comments_stmt->bindParam(':post_id', $post['post_id']);
                                    $comments_stmt->execute();
                                    $comments = $comments_stmt->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($comments as $comment) {
                                        echo "<p><strong>{$comment['username']}:</strong> {$comment['comment_text']}</p>";
                                    }
                                    ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div id="books" class="tab-content">
                    <h3>Books</h3>
                    <div class="book-grid">
                        <?php foreach ($books_list as $book): ?>
                            <div class="book-card">
                                <div class="book-image">
                                    <img src="<?= htmlspecialchars($book['cover_image'] ?: 'placeholder.jpg') ?>" alt="<?= htmlspecialchars($book['title']) ?>">
                                </div>
                                <div class="book-details">
                                    <h3 class="book-title"><?= htmlspecialchars($book['title']) ?></h3>
                                    <p>Author: <?= htmlspecialchars($book['author']) ?></p>
                                    <div class="reviews">
                                        <span>⭐⭐⭐⭐☆</span>
                                        <span class="review-count">(<?= $book['review_count'] ?? 0 ?> reviews)</span>
                                    </div>
                                    <button class="leave-review-btn" onclick="showReviewForm(<?= $book['book_id'] ?>)">Leave a Review</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div id="friends" class="tab-content">
                    <h3>Friends</h3>
                    <p>Show friends and follow suggestions here.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="review-form" id="reviewForm">
        <span class="close-review-form" onclick="closeReviewForm()">X</span>
        <h3>Write a Review</h3>
        <form method="POST" action="">
            <input type="hidden" name="book_id" id="reviewBookId">
            <textarea name="review" placeholder="Your review..." required></textarea>
            <select name="rating" required>
                <option value="">Select Rating</option>
                <option value="1">1 Star</option>
                <option value="2">2 Stars</option>
                <option value="3">3 Stars</option>
                <option value="4">4 Stars</option>
                <option value="5">5 Stars</option>
            </select>
            <button type="submit" name="submit_review">Submit Review</button>
        </form>
    </div>

    <script>
        document.getElementById('profilePicture').addEventListener('click', function() {
            const menu = document.getElementById('profileDropdownMenu');
            menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
        });

        document.getElementById('messageIcon').addEventListener('click', function() {
            const menu = document.getElementById('messageDropdownMenu');
            menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
        });

        function switchTab(tabId) {
            const tabs = document.querySelectorAll('.tab-content');
            tabs.forEach(function(tab) {
                tab.classList.remove('active');
            });
            document.getElementById(tabId).classList.add('active');
        }

        function showReviewForm(bookId) {
            document.getElementById('reviewForm').style.display = 'block';
            document.getElementById('reviewBookId').value = bookId;
        }

        function closeReviewForm() {
            document.getElementById('reviewForm').style.display = 'none';
        }
    </script>
</body>
</html>
