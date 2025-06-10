<?php
include '../includes/auth_user.php';
include '../includes/db.php';

$user_email = $_SESSION['user_email'] ?? null;
$notification_count = 0;

if ($user_email) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM notifications WHERE user_email = ?");
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $stmt->bind_result($notification_count);
    $stmt->fetch();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="../assets/css/user_styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Top Bar: Welcome & Logo -->
        <div class="top-bar">
            <h2>Welcome, <?= htmlspecialchars($_SESSION['first_name'] ?? 'User') ?> (User)</h2>
            <img src="../assets/images/hcdrd_logo.png" alt="HCDRD Logo" class="logo" />
        </div>

        <!-- Notification Bar -->
        <div class="notification-bar">
            <a href="user_notifications.php">🔔 Notifications (<?= $notification_count ?>)</a>
        </div>

        <!-- Navigation Menu -->
        <nav>
            <a href="upload_document.php" class="nav-btn">📤 Upload Document</a>
            <a href="todo_list.php" class="nav-btn">📝 To-Do List</a>
            <a href="view_status.php" class="nav-btn">📊 Track Verification Progress</a>
        </nav>

        <!-- Logout Bottom Right -->
        <div class="logout-container">
            <a href="../logout.php">🚪 Logout</a>
        </div>
    </div>
</body>
</html>