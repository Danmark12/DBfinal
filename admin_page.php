<?php
@include 'config.php'; // Include the configuration for the database connection
session_start();

// Check if the user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header('location:admin_login.php'); // Redirect to admin login page if not an admin
    exit();
}

// Fetch admin details
$admin_id = $_SESSION['admin_id'];
$query = "SELECT * FROM users WHERE user_id = :admin_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':admin_id', $admin_id);
$stmt->execute();
$admin = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin.css"> <!-- Custom CSS -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .dashboard-container {
            display: flex;
            justify-content: space-between;
            padding: 20px;
        }

        .profile-section {
            position: relative;
            display: inline-block;
        }

        .profile-section img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 60px;
            right: 0;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            z-index: 1000;
            width: 150px;
        }

        .dropdown-menu a {
            display: block;
            padding: 10px 15px;
            text-decoration: none;
            color: #333;
            border-bottom: 1px solid #eee;
        }

        .dropdown-menu a:hover {
            background-color: #f4f4f4;
        }

        .dropdown-menu a:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>

    <!-- Admin Dashboard Layout -->
    <div class="dashboard-container">
        <div class="dashboard-main">
            <h1>Admin Dashboard</h1>
            <div class="dashboard-cards">
                <div class="card">
                    <h3>Manage Users</h3>
                    <p>View, deactivate, or delete users</p>
                    <a href="manage_users.php" class="button">Manage Users</a>
                </div>
                <div class="card">
                    <h3>Manage Books</h3>
                    <p>Add, edit, or delete books</p>
                    <a href="manage_books.php" class="button">Manage Books</a>
                </div>
                <div class="card">
                    <h3>Manage Posts</h3>
                    <p>View, delete, or manage posts</p>
                    <a href="manage_posts.php" class="button">Manage Posts</a>
                </div>
                <div class="card">
                    <h3>Manage Reviews</h3>
                    <p>Approve, delete, or manage reviews</p>
                    <a href="manage_reviews.php" class="button">Manage Reviews</a>
                </div>
            </div>
        </div>

        <!-- Admin Profile Section -->
        <div class="profile-section">
            <img src="<?= $admin['profile_picture'] ?: 'default-profile.png' ?>" alt="Profile Picture" id="profilePicture">

            <!-- Dropdown Menu -->
            <div class="dropdown-menu" id="dropdownMenu">
                <a href="view_profile.php">View Profile</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>

    <script>
        // Toggle dropdown menu visibility on profile picture click
        document.getElementById('profilePicture').addEventListener('click', function () {
            const dropdownMenu = document.getElementById('dropdownMenu');
            dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
        });

        // Close the dropdown if clicking outside of it
        document.addEventListener('click', function (event) {
            const profilePicture = document.getElementById('profilePicture');
            const dropdownMenu = document.getElementById('dropdownMenu');
            if (!profilePicture.contains(event.target) && !dropdownMenu.contains(event.target)) {
                dropdownMenu.style.display = 'none';
            }
        });
    </script>

</body>
</html>
