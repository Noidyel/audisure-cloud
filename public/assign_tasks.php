<?php
session_start();
include '../includes/auth_admin.php';

$conn = new mysqli("localhost", "root", "", "audisure_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$_SESSION['status_message'] = $_SESSION['status_message'] ?? '';

// Handle task assignment
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_email = $_POST['user_email'];
    $tasks = $_POST['tasks'] ?? [];
    $custom_task = trim($_POST['custom_task']);

    if (!empty($custom_task)) {
        $tasks[] = $custom_task;
    }

    if (empty($tasks)) {
        $_SESSION['status_message'] = "❌ Please select at least one task";
    } else {
        foreach ($tasks as $task) {
            $stmt = $conn->prepare("INSERT INTO tasks (user_email, task_description, status) VALUES (?, ?, 'To-Do')");
            $stmt->bind_param("ss", $user_email, $task);
            
            if ($stmt->execute()) {
                $task_id = $stmt->insert_id;
                $task_uid = 'TSK-' . str_pad($task_id, 3, '0', STR_PAD_LEFT);

                $task_uid = 'TSK-' . str_pad($task_id, 3, '0', STR_PAD_LEFT);
                $update_stmt = $conn->prepare("UPDATE tasks SET task_uid = ? WHERE task_id = ?");
                $update_stmt->bind_param("si", $task_uid, $task_id);
                $update_stmt->execute();
                $update_stmt->close();

                $notif_stmt = $conn->prepare("INSERT INTO notifications (user_email, message, link) VALUES (?, ?, ?)");
                $message = "Admin assigned a new task: '$task'";
                $link = "todo_list.php";
                $notif_stmt->bind_param("sss", $user_email, $message, $link);
                $notif_stmt->execute();
                $notif_stmt->close();

                $_SESSION['status_message'] = "✅ Tasks assigned successfully";
            } else {
                $_SESSION['status_message'] = "❌ Error assigning tasks";
            }
            $stmt->close();
        }
    }
    header("Location: assign_tasks.php");
    exit();
}

// Handle deletion
if (isset($_GET['action'], $_GET['task_id']) && $_GET['action'] === 'delete') {
    $task_id = (int)$_GET['task_id'];
    $stmt = $conn->prepare("DELETE FROM tasks WHERE task_id = ?");
    $stmt->bind_param('i', $task_id);
    
    $_SESSION['status_message'] = $stmt->execute()
        ? "✅ Task deleted successfully"
        : "❌ Error deleting task";

    $stmt->close();
    header("Location: assign_tasks.php");
    exit();
}

// Handle archiving
if (isset($_GET['action'], $_GET['task_id']) && $_GET['action'] === 'archive') {
    $task_id = (int)$_GET['task_id'];

    $get_task = $conn->prepare("SELECT task_id, task_uid, user_email, task_description, status FROM tasks WHERE task_id = ?");
    $get_task->bind_param('i', $task_id);
    $get_task->execute();
    $task_data = $get_task->get_result()->fetch_assoc();
    $get_task->close();

    if ($task_data) {
        $archive_stmt = $conn->prepare("INSERT INTO archived_tasks (original_task_id, user_email, task_description, status, archived_at) VALUES (?, ?, ?, ?, NOW())");
        $archive_stmt->bind_param('ssss',
            $task_data['task_uid'],
            $task_data['user_email'],
            $task_data['task_description'],
            $task_data['status']
        );

        if ($archive_stmt->execute()) {
            $delete_stmt = $conn->prepare("DELETE FROM tasks WHERE task_id = ?");
            $delete_stmt->bind_param('i', $task_id);
            $delete_stmt->execute();
            $delete_stmt->close();

            $notif_stmt = $conn->prepare("INSERT INTO notifications (user_email, message, link) VALUES (?, ?, ?)");
            $message = "Your task '{$task_data['task_description']}' has been archived";
            $link = "todo_list.php";
            $notif_stmt->bind_param("sss", $task_data['user_email'], $message, $link);
            $notif_stmt->execute();
            $notif_stmt->close();

            $_SESSION['status_message'] = "✅ Task archived successfully";
        } else {
            $_SESSION['status_message'] = "❌ Error archiving task";
        }
        $archive_stmt->close();
    } else {
        $_SESSION['status_message'] = "❌ Task not found";
    }

    header("Location: assign_tasks.php");
    exit();
}

// Fix any existing tasks without proper task_uid
$fix_task_uids = $conn->query("SELECT task_id, task_uid FROM tasks WHERE task_uid IS NULL OR task_uid = '' OR task_uid = '0'");
if ($fix_task_uids && $fix_task_uids->num_rows > 0) {
    while ($row = $fix_task_uids->fetch_assoc()) {
        $task_id = $row['task_id'];
        $task_uid = 'TSK-' . str_pad($task_id, 3, '0', STR_PAD_LEFT);
        
        $update_stmt = $conn->prepare("UPDATE tasks SET task_uid = ? WHERE task_id = ?");
        $update_stmt->bind_param("si", $task_uid, $task_id);
        $update_stmt->execute();
        $update_stmt->close();
    }
}

// Fetch users and tasks
$users = $conn->query("SELECT first_name, last_name, email FROM users WHERE status = 'Approved'");
$task_results = $conn->query("SELECT * FROM tasks ORDER BY assigned_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Tasks - HCDRD Admin</title>
    <link rel="stylesheet" href="../assets/css/admin_styles.css">
    <style>
        .top-bar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .top-bar img {
            height: 50px;
        }

        .container {
            max-width: 1000px;
            margin: 30px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        h2 {
            text-align: center;
            color: #D62828;
            margin-bottom: 25px;
            font-size: 28px;
        }

        .status-message {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            text-align: center;
            font-weight: 600;
        }

        .status-success {
            background-color: #d4edda;
            color: #155724;
        }

        .status-error {
            background-color: #f8d7da;
            color: #721c24;
        }

        form {
            margin-bottom: 40px;
            background: #f9f9f9;
            padding: 25px;
            border-radius: 8px;
        }

        label {
            display: block;
            margin: 15px 0 5px;
            font-weight: 600;
            color: #333;
        }

        select, input[type="text"], button {
            width: 100%;
            padding: 12px;
            margin-top: 8px;
            border-radius: 6px;
            border: 1px solid #ddd;
            font-size: 16px;
            box-sizing: border-box;
        }

        input[type="checkbox"] {
            margin-right: 10px;
            transform: scale(1.2);
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            margin: 10px 0;
            cursor: pointer;
        }

        button {
            background-color: #D62828;
            color: white;
            font-weight: bold;
            margin-top: 25px;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s ease;
            padding: 14px;
            font-size: 16px;
        }

        button:hover {
            background-color: #a61717;
        }

        .task-list {
            margin-top: 40px;
        }

        .task-list h3 {
            color: #D62828;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .task-item {
            background: #f8f8f8;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 6px solid #D62828;
            border-radius: 6px;
            position: relative;
        }

        .task-item strong {
            display: block;
            margin-bottom: 8px;
            font-size: 18px;
        }

        .status {
            font-weight: bold;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 14px;
        }

        .status-todo {
            color: #ffc107;
        }

        .status-done {
            color: #28a745;
        }

        .status-archived {
            color: #6c757d;
        }

        .action-links {
            position: absolute;
            right: 20px;
            top: 20px;
        }

        .edit-link, .delete-link, .archive-link {
            font-size: 14px;
            margin-left: 15px;
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .edit-link {
            color: #fff;
            background-color: #17a2b8;
        }

        .edit-link:hover {
            background-color: #138496;
        }

        .delete-link {
            color: #fff;
            background-color: #dc3545;
        }

        .delete-link:hover {
            background-color: #c82333;
        }

        .archive-link {
            color: #fff;
            background-color: #6c757d;
        }

        .archive-link:hover {
            background-color: #5a6268;
        }

        .back-button-container {
            text-align: center;
            margin-top: 40px;
        }

        .back-button {
            background-color: #D62828;
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.2s;
        }

        .back-button:hover {
            background-color: #a61717;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
                margin: 15px;
            }
            
            .task-item {
                padding: 15px;
            }
            
            .action-links {
                position: static;
                margin-top: 10px;
                display: flex;
                justify-content: flex-end;
            }
            
            .edit-link, .delete-link, .archive-link {
                margin-left: 10px;
            }
        }
    </style>
</head>
<body>

<div class="top-bar">
    <img src="../assets/images/hcdrd_logo.png" alt="HCDRD Logo">
</div>

<div class="container">
    <h2>Assign Tasks</h2>

    <?php if (!empty($_SESSION['status_message'])): ?>
        <div class="status-message <?= strpos($_SESSION['status_message'], '❌') !== false ? 'status-error' : 'status-success' ?>">
            <?= htmlspecialchars($_SESSION['status_message']) ?>
        </div>
        <?php unset($_SESSION['status_message']); ?>
    <?php endif; ?>

    <form method="POST" action="assign_tasks.php">
        <label for="user_email">Assign To:</label>
        <select name="user_email" required>
            <option value="">-- Select User --</option>
            <?php while($user = $users->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($user['email']) ?>">
                    <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?> (<?= htmlspecialchars($user['email']) ?>)
                </option>
            <?php endwhile; ?>
        </select>

        <label><strong>Select Tasks:</strong></label>
        <label class="checkbox-label"><input type="checkbox" name="tasks[]" value="Review Logs"> Review Audit Logs</label>
        <label class="checkbox-label"><input type="checkbox" name="tasks[]" value="Verify Documents"> Verify Documents</label>
        <label class="checkbox-label"><input type="checkbox" name="tasks[]" value="Generate Report"> Generate Report</label>
        <label class="checkbox-label"><input type="checkbox" name="tasks[]" value="Flag Inconsistencies"> Flag Inconsistencies</label>

        <label for="custom_task"><strong>Custom Task:</strong></label>
        <input type="text" name="custom_task" placeholder="Enter a custom task if not listed above...">

        <button type="submit">Assign Task</button>
    </form>

    <div class="task-list">
        <h3>Recently Assigned Tasks</h3>
        <?php while ($task = $task_results->fetch_assoc()): ?>
            <?php 
                // Make sure task_uid is correctly formatted
                $display_task_uid = !empty($task['task_uid']) ? $task['task_uid'] : 'TSK-' . str_pad($task['task_id'], 3, '0', STR_PAD_LEFT);
            ?>
            <div class="task-item">
                <strong>Task ID: <?= htmlspecialchars($display_task_uid) ?></strong>
                <div><?= htmlspecialchars($task['task_description']) ?></div>
                <div>Assigned to: <?= htmlspecialchars($task['user_email']) ?></div>
                <div>Status: <span class="status status-<?= strtolower($task['status']) ?>"><?= htmlspecialchars($task['status']) ?></span></div>
                
                <div class="action-links">
                    <a href="edit_tasks.php?task_id=<?= $task['task_id'] ?>" class="edit-link">Edit</a>
                    <a href="?action=archive&task_id=<?= $task['task_id'] ?>" class="archive-link" onclick="return confirm('Are you sure you want to archive this task?')">Archive</a>
                    <?php if ($task['status'] === 'Done'): ?>
                        <a href="?action=delete&task_id=<?= $task['task_id'] ?>" class="delete-link" onclick="return confirm('Are you sure you want to delete this task?')">Delete</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <div class="back-button-container">
        <a href="admin_dashboard.php" class="back-button">← Back to Dashboard</a>
    </div>
</div>

</body>
</html>