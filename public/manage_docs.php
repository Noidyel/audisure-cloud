<?php 
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin' || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('../includes/db.php');
include('../includes/auth_admin.php');
include('../includes/notifications_helper.php');

// Handle status update
if (isset($_GET['action'], $_GET['id'])) {
    $doc_id = $_GET['id']; // Keep as string (not intval)
    $action = strtolower($_GET['action']);

    if (in_array($action, ['approved', 'rejected', 'pending'])) {
        $changed_by = $_SESSION['user_id'];
        $changed_at = date('Y-m-d H:i:s');

        // Check if document_uid exists in documents
        $stmt = $conn->prepare("SELECT 1 FROM documents WHERE document_uid = ?");
        $stmt->bind_param("s", $doc_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            $stmt->close();
            $_SESSION['status_message'] = "❌ Error: Document ID $doc_id does not exist.";
            header("Location: manage_docs.php");
            exit();
        }
        $stmt->close();

        // Get the latest status
        $stmt = $conn->prepare("SELECT status FROM documents_statuses WHERE document_uid = ? ORDER BY updated_at DESC LIMIT 1");
        $stmt->bind_param("s", $doc_id);
        $stmt->execute();
        $stmt->bind_result($current_status);
        $stmt->fetch();
        $stmt->close();

        if ($current_status === $action) {
            $_SESSION['status_message'] = "ℹ️ The document is already '" . ucfirst($action) . "'.";
        } elseif ($current_status === 'approved') {
            $_SESSION['status_message'] = "⚠️ The document has already been approved. No further changes are allowed.";
        } else {
            // Insert new status
            $stmt = $conn->prepare("INSERT INTO documents_statuses (document_uid, status, changed_by, updated_at) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssis", $doc_id, $action, $changed_by, $changed_at);

            try {
                $stmt->execute();
            } catch (mysqli_sql_exception $e) {
                $_SESSION['status_message'] = "❌ Error inserting status: " . $e->getMessage();
                $stmt->close();
                header("Location: manage_docs.php");
                exit();
            }
            $stmt->close();

            // Notify uploader
            $stmt = $conn->prepare("SELECT u.email FROM documents d JOIN users u ON d.user_id = u.id WHERE d.document_uid = ?");
            $stmt->bind_param("s", $doc_id);
            $stmt->execute();
            $stmt->bind_result($user_email);
            $stmt->fetch();
            $stmt->close();

            if (!empty($user_email)) {
                $statusMessage = ucfirst($action);
                $message = "Your document (ID: $doc_id) has been $statusMessage.";
                addNotification($conn, $user_email, 'user', $message, 'user_documents.php');
            }

            $_SESSION['status_message'] = "✅ Changed status to '" . ucfirst($action) . "'";
        }

        header("Location: manage_docs.php");
        exit();
    }
}

// Fetch document list with latest status
$query = "SELECT d.document_uid AS id, d.file_path, d.created_at, 
                 (SELECT status FROM documents_statuses ds WHERE ds.document_uid = d.document_uid ORDER BY ds.updated_at DESC LIMIT 1) AS status, 
                 u.first_name, u.last_name 
          FROM documents d 
          JOIN users u ON d.user_id = u.id 
          ORDER BY d.created_at DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Documents - HCDRD Admin</title>
    <link rel="stylesheet" href="admin_styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h2>Manage Documents - HCDRD Admin Panel</h2>
            <img src="../assets/images/hcdrd_logo.png" alt="HCDRD Logo" class="logo">
        </header>

        <?php if (isset($_SESSION['status_message'])): ?>
            <div class="status-message">
                <?= htmlspecialchars($_SESSION['status_message']) ?>
                <?php unset($_SESSION['status_message']); ?>
            </div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th><b>ID</b></th>
                    <th><b>User</b></th>
                    <th><b>File</b></th>
                    <th><b>Status</b></th>
                    <th><b>Uploaded</b></th>
                    <th><b>Actions</b></th>
                </tr>
            </thead>
            <tbody>
                <?php while ($doc = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($doc['id']) ?></td>
                    <td><?= htmlspecialchars($doc['first_name'] . ' ' . $doc['last_name']) ?></td>
                    <td><a href="../uploads/<?= htmlspecialchars($doc['file_path']) ?>" target="_blank"><?= htmlspecialchars($doc['file_path']) ?></a></td>
                    <td><?= $doc['status'] ? ucfirst($doc['status']) : 'Pending' ?></td>
                    <td><?= htmlspecialchars($doc['created_at']) ?></td>
                    <td>
                        <a href="?action=approved&id=<?= $doc['id'] ?>" class="approve-btn">Approve</a> |
                        <a href="?action=pending&id=<?= $doc['id'] ?>" class="pending-btn">Pending</a> |
                        <a href="?action=rejected&id=<?= $doc['id'] ?>" class="reject-btn">Reject</a>
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