<?php
// Start the session if it is not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debugging: Check if session variables are set
error_log('Session data: ' . print_r($_SESSION, true)); // Logs the session array to check if variables are set
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Debugging message
    error_log("Session variables not set or role is not admin.");
    
    // Redirect to login page or homepage if not admin
    header("Location: ../index.php");
    exit();
}
?>
