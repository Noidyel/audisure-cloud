<?php
include('../includes/db.php');
include('../includes/auth_admin.php');

// Action handler
if (isset($_GET['action'], $_GET['id'])) {
    $id = $_GET['id'];
    $status = ($_GET['action'] === 'approve') ? 'approved' : 'rejected';

    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->bind_param('si', $status, $id);
    $stmt->execute();
    header("Location: verify_users.php");
    exit;
}

$result = $conn->query("SELECT * FROM users WHERE status = 'pending'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Users</title>
    <link rel="stylesheet" href="../assets/css/admin_styles.css">
</head>
<body>

<!-- Top bar with logo -->
<div class="top-bar">
    <img src="../assets/images/hcdrd_logo.png" alt="HCDRD Logo">
</div>

<!-- Main Content -->
<div class="content-container">
    <h2>Verify Users</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['first_name']) ?></td>
                    <td><?= htmlspecialchars($user['last_name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['status']) ?></td>
                    <td>
                        <a href="?action=approve&id=<?= $user['id'] ?>">Approve</a> |
                        <a href="?action=reject&id=<?= $user['id'] ?>">Reject</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="back-button-container">
        <a href="admin_dashboard.php" class="back-button">← Back to Dashboard</a>
    </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const approveLinks = document.querySelectorAll('a[href*="action=approve"]');
    const rejectLinks = document.querySelectorAll('a[href*="action=reject"]');

    approveLinks.forEach(link => {
      link.addEventListener('click', e => {
        if (!confirm('Are you sure you want to APPROVE this user?')) {
          e.preventDefault();
        }
      });
    });

    rejectLinks.forEach(link => {
      link.addEventListener('click', e => {
        if (!confirm('Are you sure you want to REJECT this user?')) {
          e.preventDefault();
        }
      });
    });
  });
</script>

</body>
</html>
