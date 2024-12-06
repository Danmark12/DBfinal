<?php
@include 'config.php';
session_start();

// Check if the user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header('location:admin_login.php');
    exit();
}

// Fetch all users
$users = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
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
        <h1>Manage Users</h1>
        
        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php while ($user = $users->fetch(PDO::FETCH_ASSOC)) { ?>
                <tr>
                    <td><?= $user['user_id'] ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= $user['role'] ?></td>
                    <td><?= $user['is_active'] ? 'Active' : 'Inactive' ?></td>
                    <td>
                        <a href="deactivate_user.php?id=<?= $user['user_id'] ?>" class="button">Deactivate</a> |
                        <a href="delete_user.php?id=<?= $user['user_id'] ?>" class="button">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>

        <br>
        <a href="admin_page.php" class="button">Back to Admin Page</a>
    </div>

</body>
</html>
