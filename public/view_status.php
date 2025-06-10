<?php
include '../includes/auth_user.php';
include '../includes/db.php'; // make sure $conn is your pg connection resource

$user_id = $_SESSION['user_id'];

// Fetch the latest status of documents uploaded by the user
$sql = "
    SELECT d.title, d.created_at,
        (
            SELECT ds.status
            FROM documents_statuses ds
            WHERE ds.document_uid = d.document_uid
            ORDER BY ds.updated_at DESC
            LIMIT 1
        ) AS status
    FROM documents d
    WHERE d.user_id = $1
    ORDER BY d.created_at DESC
";

$result = pg_query_params($conn, $sql, array($user_id));

if (!$result) {
    die("Error in SQL query: " . pg_last_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document Verification Status</title>
    <link rel="stylesheet" href="../assets/css/user_styles.css">
</head>
<body>
    <div class="container">
        <header>
            <img src="../assets/images/hcdrd_logo.png" alt="HCDRD Logo" class="logo">
        </header>

        <h2>Document Verification Status</h2>

        <?php if (pg_num_rows($result) > 0): ?>
            <table>
                <tr>
                    <th>Document Title</th>
                    <th>Uploaded At</th>
                    <th>Status</th>
                </tr>
                <?php while ($row = pg_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['created_at']) ?></td>
                        <td>
                            <?php
                            switch ($row['status']) {
                                case 'approved':
                                    echo "<span class='approved'>Approved</span>";
                                    break;
                                case 'rejected':
                                    echo "<span class='rejected'>Rejected</span>";
                                    break;
                                case 'needs_editing':
                                    echo "<span class='needs-editing'>Needs Editing</span>";
                                    break;
                                case 'processing':
                                    echo "<span class='processing'>Processing</span>";
                                    break;
                                case 'in_review':
                                    echo "<span class='in-review'>In Review</span>";
                                    break;
                                default:
                                    echo "<span class='pending'>Pending</span>";
                                    break;
                            }
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p style="text-align: center;">You haven't uploaded any documents yet.</p>
        <?php endif; ?>

        <div style="text-align: center;">
            <a href="user_dashboard.php" class="back-link">← Back to Dashboard</a>
        </div>
    </div>
</body>
</html>

<?php
pg_free_result($result);
pg_close($conn);
?>
