<?php
session_start();

// Redirect if not logged in or not approved
if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['user_status']) ||
    $_SESSION['user_status'] !== 'approved'
) {
    header("Location: ../login.php");
    exit;
}

// Redirect if role is not 'user'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: ../admin/admin_dashboard.php");
    exit;
}
?>
