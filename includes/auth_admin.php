<?php
// Start the session if it is not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Optional: Disable error logging in production or remove after debugging
// error_log('Session data: ' . print_r($_SESSION, true));

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    // Optional: error_log("Unauthorized admin access attempt.");
    header("Location: ../index.php");
    exit();
}
  