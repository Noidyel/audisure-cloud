<?php
include '../includes/auth_user.php';
include '../includes/db.php';

$user_email = $_SESSION['user_email'];

// Fetch notifications for the user from the notifications table
$stmt = $conn->prepare("SELECT * FROM notifications WHERE user_email = ? AND role = 'user' ORDER BY created_at DESC");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Notifications</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/users_styles.css">
</head>
<body>
    <div class="container">
        <!-- Top Bar -->
        <div class="top-bar">
            <h2>Your Notifications</h2>
            <img src="../assets/images/hcdrd_logo.png" alt="HCDRD Logo" class="logo">
        </div>

        <?php if ($result->num_rows > 0): ?>
            <ul>
                <?php while ($notif = $result->fetch_assoc()): ?>
                    <li>
                        <span><?= htmlspecialchars($notif['message']) ?></span>
                        <small><?= $notif['created_at'] ?></small>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No notifications yet.</p>
        <?php endif; ?>

        <a href="user_dashboard.php" class="back-link">← Back to Dashboard</a>
    </div>
</body>
</html>
