<?php
@include 'config.php';
session_start();

// Check if the user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header('location:admin_login.php');
    exit();
}

// Fetch admin details
$admin_id = $_SESSION['admin_id'];
$query = "SELECT * FROM users WHERE user_id = :admin_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':admin_id', $admin_id);
$stmt->execute();
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// Update profile picture (if any)
if (isset($_POST['update_profile'])) {
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $profile_picture = 'uploads/' . basename($_FILES['profile_picture']['name']);
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture);
        
        $update_query = "UPDATE users SET profile_picture = :profile_picture WHERE user_id = :admin_id";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bindParam(':profile_picture', $profile_picture);
        $update_stmt->bindParam(':admin_id', $admin_id);
        
        if ($update_stmt->execute()) {
            $success = "Profile updated successfully!";
            header('Location: admin_dashboard.php');
        } else {
            $error = "Failed to update the profile picture.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            width: 50%;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
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
        <h1>Edit Profile</h1>

        <?php if (isset($success)) { echo "<p style='color: green;'>$success</p>"; } ?>
        <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <div>
                <label for="profile_picture">Profile Picture</label><br>
                <input type="file" name="profile_picture" id="profile_picture" required><br><br>
                <img src="<?= $admin['profile_picture'] ?>" alt="Current Profile Picture" width="100"><br><br>
            </div>
            <button type="submit" name="update_profile" class="button">Update Profile</button>
        </form>

        <br>
        <a href="admin_page.php" class="button">Back to Admin Page</a>
    </div>

</body>
</html>
