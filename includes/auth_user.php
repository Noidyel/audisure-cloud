<?php
session_start();

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['user_status']) ||
    $_SESSION['user_status'] !== 'approved' ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'user'
) {
    // If user is not approved or role mismatch, redirect accordingly
    // If role is admin, redirect to admin dashboard
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        header("Location: ../admin/admin_dashboard.php");
    } else {
        header("Location: ../login.php");
    }
    exit();
}
  