<?php
@include 'config.php'; // Include the configuration file for the PDO connection

session_start();

// Redirect to user page if already logged in
if (isset($_SESSION['user_id'])) {
    header('location:user_page.php');
    exit();
}

if (isset($_POST['submit'])) {
    // Retrieve form data
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Check if user exists in the database
    $select = "SELECT * FROM users WHERE email = :email AND is_active = 1"; // Check if user is active
    $stmt = $conn->prepare($select);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verify password using password_verify
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['user_id'];  // Store user ID in session
            $_SESSION['username'] = $row['username']; // Store username in session

            // Redirect based on user role (admin or user)
            if ($row['role'] == 'admin') {
                $_SESSION['admin_id'] = $row['user_id']; // Store admin ID in session
                header('location:admin_page.php');
            } else {
                header('location:user_page.php');
            }
            exit();
        } else {
            $error[] = 'Incorrect email or password!';
        }
    } else {
        $error[] = 'User not found or account inactive!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>

    <link rel="stylesheet" href="css/style.css"> <!-- Link your CSS file -->
</head>
<body>
    <div class="form-container">
        <form action="" method="post">
            <h3>Login Now</h3>

            <?php
            // Display error messages if any
            if (isset($error)) {
                foreach ($error as $error) {
                    echo '<span class="error-msg">' . htmlspecialchars($error) . '</span>';
                };
            };
            ?>

            <!-- Form fields -->
            <input type="email" name="email" required placeholder="Enter your email">
            <input type="password" name="password" required placeholder="Enter your password">
            <input type="submit" name="submit" value="Login Now" class="form-btn">
            <p>Don't have an account? <a href="register_form.php">Register Now</a></p>
        </form>
    </div>
</body>
</html>
