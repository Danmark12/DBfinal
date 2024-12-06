<?php
$host = 'localhost'; // Database server (localhost for XAMPP)
$username = 'root';  // Your MySQL username
$password = '';      // Your MySQL password (empty by default on XAMPP)
$db_name = 'user_system'; // The name of your database

try {
    // Create a new PDO connection
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    // Set PDO to throw exceptions on errors
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Catch any errors and display the message
    echo "Connection failed: " . $e->getMessage();
    exit(); // Stop the execution if database connection fails
}
?>
