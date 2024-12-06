<?php
// Start the session to check if the user is logged in
session_start();

// If the user is logged in, redirect to their respective dashboard
if (isset($_SESSION['user_name'])) {
    // If logged in as a user, redirect to user page
    header('Location: user_page.php');
    exit();
} elseif (isset($_SESSION['admin_name'])) {
    // If logged in as an admin, redirect to admin page
    header('Location: admin_page.php');
    exit();
}

// If the user is not logged in, show the landing page
header('Location: landing_page.php');
exit();
?>
