<?php
include '../includes/auth_user.php';
include '../includes/db.php';

if (!isset($_SESSION['user_email'])) {
    echo "Error: User email not found in session.";
    exit;
}

$user_email = $_SESSION['user_email'];

// Handle task status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id'], $_POST['status'])) {
    $task_id = (int)$_POST['task_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE tasks SET status = ? WHERE task_id = ?");
    $stmt->bind_param('si', $status, $task_id);
    $stmt->execute();

    header("Location: todo_list.php");
    exit;
}

// Handle archive
if (isset($_GET['action'], $_GET['task_id'])) {
    $task_id = (int)$_GET['task_id'];
    $action = $_GET['action'];

// In the archiving section, modify this part:
    if ($action === 'archive') {
        // Get the task data first - include task_uid in the SELECT
        $stmt = $conn->prepare("SELECT task_uid, user_email, task_description, status FROM tasks WHERE task_id = ?");
        $stmt->bind_param('i', $task_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $task = $result->fetch_assoc();
        $stmt->close();
    
        if ($task) {
            // Insert into archived_tasks - use task_uid instead of id
            $archive_stmt = $conn->prepare("INSERT INTO archived_tasks (original_task_id, user_email, task_description, status, archived_at) VALUES (?, ?, ?, ?, NOW())");
            $archive_stmt->bind_param('ssss', 
                $task['task_uid'],
                $task['user_email'],
                $task['task_description'],
                $task['status']
            );            

            // Delete from tasks table
            $delete_stmt = $conn->prepare("DELETE FROM tasks WHERE task_id = ?");
            $delete_stmt->bind_param('i', $task_id);
            $delete_stmt->execute();
            $delete_stmt->close();
        }
    }

    header("Location: todo_list.php");
    exit;
}

// Fetch tasks
$sql = "SELECT * FROM tasks WHERE user_email = ? ORDER BY assigned_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $user_email);
$stmt->execute();
$task_results = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>To-Do List</title>
    <link rel="stylesheet" href="../assets/css/users_styles.css">
</head>
<body>
<div class="container">
    <header>
        <img src="../assets/images/hcdrd_logo.png" alt="HCDRD Logo" class="logo">
    </header>

    <h2>Your Assigned Tasks</h2>

    <div class="reminder-note">
        <p><strong>Reminder:</strong> Update your tasks using the available actions below.</p>
    </div>

    <?php if ($task_results->num_rows > 0): ?>
        <table>
            <tr>
                <th>Task Description</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php while ($task = $task_results->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($task['task_description']) ?></td>
                    <td><?= htmlspecialchars($task['status']) ?></td>
                    <td>
                        <?php if ($task['status'] === 'To-Do'): ?>
                            <form method="POST" class="action-form">
                                <input type="hidden" name="task_id" value="<?= $task['task_id'] ?>">
                                <label class="status-label">
                                    <input type="radio" name="status" value="In Progress" onchange="this.form.submit()"> In Progress
                                </label>
                            </form>
                        <?php elseif ($task['status'] === 'In Progress'): ?>
                            <form method="POST" class="action-form">
                                <input type="hidden" name="task_id" value="<?= $task['task_id'] ?>">
                                <label class="status-label">
                                    <input type="radio" name="status" value="Completed" onchange="this.form.submit()"> Completed
                                </label>
                            </form>
                        <?php elseif ($task['status'] === 'Completed'): ?>
                            <a href="?action=archive&task_id=<?= $task['task_id'] ?>" class="archive-link" onclick="return confirm('Are you sure you want to archive this task?')">Archive</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No tasks assigned.</p>
    <?php endif; ?>

    <a href="user_dashboard.php" class="back-link">← Back to Dashboard</a>
</div>
</body>
</html>

<?php $conn->close(); ?>