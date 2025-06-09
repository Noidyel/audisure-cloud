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
    <style>
        .top-bar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 20px;
        }

        .top-bar img {
            height: 50px;
        }

        .content-container {
            max-width: 1000px;
            margin: 40px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        h2 {
            text-align: center;
            color: #D62828;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 14px 18px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #D62828;
            color: white;
        }

        td a {
            color: #D62828;
            font-weight: 500;
            text-decoration: none;
        }

        td a:hover {
            text-decoration: underline;
            color: #a61717;
        }

        .back-button-container {
            text-align: center;
            margin-top: 40px;
        }

        .back-button {
            text-decoration: none;
            background-color: #D62828;
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 500;
            transition: background-color 0.2s ease-in-out;
        }

        .back-button:hover {
            background-color: #a61717;
        }
    </style>
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
                        <a href="?action=rejected&id=<?= $user['id'] ?>">Reject</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="back-button-container">
        <a href="admin_dashboard.php" class="back-button">← Back to Dashboard</a>
    </div>
</div>

</body>
</html>
