<?php
@include 'config.php'; // Include the configuration file for the PDO connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $username = htmlspecialchars(trim($_POST['username']));
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password']; // Plain text password
    $cpassword = $_POST['cpassword']; // Plain text confirm password
    $role = $_POST['role']; // Role: user or admin

    $error = []; // Initialize error array

    // Validate inputs
    if (!$email) {
        $error[] = 'Please enter a valid email address.';
    }
    if (strlen($password) < 8) {
        $error[] = 'Password must be at least 8 characters.';
    }
    if ($password !== $cpassword) {
        $error[] = 'Passwords do not match!';
    }

    if (empty($error)) {
        try {
            // Check if the user already exists
            $select = "SELECT * FROM users WHERE email = :email";
            $stmt = $conn->prepare($select);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $error[] = 'User already exists!';
            } else {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert the new user into the database
                $insert = "INSERT INTO users (username, email, password, role, is_active) 
                           VALUES (:username, :email, :password, :role, :is_active)";
                $stmt = $conn->prepare($insert);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $hashed_password);
                $stmt->bindParam(':role', $role);
                $stmt->bindValue(':is_active', 1); // Set account as active by default

                $stmt->execute();
                header('location:login.php');
                exit();
            }
        } catch (PDOException $e) {
            $error[] = 'An error occurred: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Form</title>

    <!-- Custom CSS file link -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="form-container">
        <form action="" method="post">
            <h3>Register Now</h3>

            <?php
            // Display error messages if any
            if (isset($error)) {
                foreach ($error as $error) {
                    echo '<span class="error-msg">' . htmlspecialchars($error) . '</span>';
                };
            };
            ?>

            <!-- Form fields -->
            <input type="text" name="username" required placeholder="Enter your name">
            <input type="email" name="email" required placeholder="Enter your email">
            <input type="password" name="password" required placeholder="Enter your password">
            <input type="password" name="cpassword" required placeholder="Confirm your password">
            <select name="role" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
            <input type="submit" name="submit" value="Register Now" class="form-btn">
            <p>Already have an account? <a href="login.php">Login Now</a></p>
        </form>
    </div>
</body>
</html>
