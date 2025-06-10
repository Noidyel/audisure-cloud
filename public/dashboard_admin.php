<?php
session_start();
include '../includes/auth_admin.php';
include('../includes/db.php');

// Fetch unread notifications count for admin
$stmt = $conn->prepare("SELECT COUNT(*) FROM notifications WHERE status = 'unread' AND role = 'admin'");
$stmt->execute();
$stmt->bind_result($unread_count);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/admin_styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>

<div class="container">
    <!-- Top Bar -->
    <div class="top-bar">
        <h2>Welcome, <?= htmlspecialchars($_SESSION['first_name'] ?? 'Admin') ?></h2>
        <img src="../assets/images/hcdrd_logo.png" alt="HCDRD Logo" class="logo">
    </div>

    <!-- Notifications -->
    <div class="notification-bar">
        <a href="admin_notifications.php">🔔 Notifications (<?= $unread_count ?>)</a>
    </div>

    <!-- Navigation -->
    <nav class="admin-nav">
        <a href="verify_users.php" class="nav-btn">👥 Verify User Accounts</a>
        <a href="assign_tasks.php" class="nav-btn">📝 Assign Tasks</a>
        <a href="manage_docs.php" class="nav-btn">📂 Manage Documents</a>
    </nav>

    <!-- Logout -->
    <div class="logout-container">
        <a href="../logout.php">🚪 Logout</a>
    </div>
</div>

</body>
</html>
