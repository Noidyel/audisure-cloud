<?php
include '../includes/auth_user.php';
include '../includes/db.php'; // $conn is pg connection resource

if (!isset($_SESSION['user_email'])) {
    echo "Error: User email not found in session.";
    exit;
}

$user_email = $_SESSION['user_email'];

// Handle task status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id'], $_POST['status'])) {
    $task_id = (int)$_POST['task_id'];
    $status = $_POST['status'];

    $update_sql = "UPDATE tasks SET status = $1 WHERE task_id = $2";
    $update_result = pg_query_params($conn, $update_sql, array($status, $task_id));

    if (!$update_result) {
        die("Error updating task status: " . pg_last_error($conn));
    }

    header("Location: todo_list.php");
    exit;
}

// Handle archive
if (isset($_GET['action'], $_GET['task_id'])) {
    $task_id = (int)$_GET['task_id'];
    $action = $_GET['action'];

    if ($action === 'archive') {
        // Get the task data first - include task_uid in SELECT
        $select_sql = "SELECT task_uid, user_email, task_description, status FROM tasks WHERE task_id = $1";
        $select_result = pg_query_params($conn, $select_sql, array($task_id));

        if ($select_result && pg_num_rows($select_result) > 0) {
            $task = pg_fetch_assoc($select_result);

            // Insert into archived_tasks - use task_uid instead of id
            $insert_sql = "INSERT INTO archived_tasks (original_task_id, user_email, task_description, status, archived_at) VALUES ($1, $2, $3, $4, NOW())";
            $insert_result = pg_query_params($conn, $insert_sql, array(
                $task['task_uid'],
                $task['user_email'],
                $task['task_description'],
                $task['status']
            ));

            if (!$insert_result) {
                die("Error archiving task: " . pg_last_error($conn));
            }

            // Delete from tasks table
            $delete_sql = "DELETE FROM tasks WHERE task_id = $1";
            $delete_result = pg_query_params($conn, $delete_sql, array($task_id));

            if (!$delete_result) {
                die("Error deleting task: " . pg_last_error($conn));
            }
        }
    }

    header("Location: todo_list.php");
    exit;
}

// Fetch tasks
$fetch_sql = "SELECT * FROM tasks WHERE user_email = $1 ORDER BY assigned_at DESC";
$task_results = pg_query_params($conn, $fetch_sql, array($user_email));

if (!$task_results) {
    die("Error fetching tasks: " . pg_last_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>To-Do List</title>
    <link rel="stylesheet" href="../assets/css/user_styles.css">
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

    <?php if (pg_num_rows($task_results) > 0): ?>
        <table>
            <tr>
                <th>Task Description</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php while ($task = pg_fetch_assoc($task_results)): ?>
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

<?php
pg_free_result($task_results);
pg_close($conn);
?>
