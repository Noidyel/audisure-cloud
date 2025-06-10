<?php
include '../includes/auth_user.php';
include '../includes/db.php';

$user_email = $_SESSION['user_email'];

// Fetch notifications for the user from the notifications table
$sql = "SELECT * FROM notifications WHERE user_email = $1 AND role = 'user' ORDER BY created_at DESC";
$result = pg_query_params($conn, $sql, array($user_email));

if (!$result) {
    die("Error fetching notifications: " . pg_last_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>User Notifications</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../assets/css/user_styles.css" />
</head>
<body>
    <div class="container">
        <!-- Top Bar -->
        <div class="top-bar">
            <h2>Your Notifications</h2>
            <img src="../assets/images/hcdrd_logo.png" alt="HCDRD Logo" class="logo" />
        </div>

        <?php if (pg_num_rows($result) > 0): ?>
            <ul>
                <?php while ($notif = pg_fetch_assoc($result)): ?>
                    <li>
                        <span><?= htmlspecialchars($notif['message']) ?></span>
                        <small><?= htmlspecialchars($notif['created_at']) ?></small>
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

<?php
pg_free_result($result);
pg_close($conn);
?>
