<?php
session_start();
include '../includes/db.php';

// Check if user is logged in and if the role is 'admin'
if (!isset($_SESSION['user_email']) || $_SESSION['role'] !== 'admin') {
    header('Location: admin_dashboard.php');
    exit;
}

$admin_email = $_SESSION['user_email'];

// Fetch admin notifications
$stmt = $conn->prepare("SELECT * FROM notifications WHERE user_email = ? AND role = 'admin' ORDER BY created_at DESC");
$stmt->bind_param("s", $admin_email);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Notifications</title>
    <link rel="stylesheet" href="../assets/css/admin_styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h2>🔔 Admin Notifications</h2>
        </header>

        <?php if ($result->num_rows > 0): ?>
            <ul class="notifications-list">
                <?php while ($notif = $result->fetch_assoc()): ?>
                    <li>
                        <?= htmlspecialchars($notif['message']) ?>
                        <?php if (!empty($notif['link'])): ?>
                            - <a href="<?= htmlspecialchars($notif['link']) ?>">View</a>
                        <?php endif; ?>
                        <small><?= htmlspecialchars($notif['created_at']) ?></small>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No notifications yet.</p>
        <?php endif; ?>

        <a href="dashboard_admin.php" class="back-link">← Back to Dashboard</a>
    </div>
</body>
</html>
