<?php
session_start();
include '../includes/auth_admin.php';  // Ensure admin check is performed before any further action
include '../includes/db.php';        // Database connection

// Check if task_id is provided in the URL
if (isset($_GET['task_id'])) {
    $task_id = (int)$_GET['task_id'];

    // Fetch task details from the database using the correct column name 'task_id'
    $sql = "SELECT * FROM tasks WHERE task_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $task_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $task = $result->fetch_assoc();

    // Check if task exists
    if (!$task) {
        echo "Task not found. <a href='assign_tasks.php'>Go back to task list.</a>";
        exit;
    }
} else {
    echo "Task ID not provided. <a href='assign_tasks.php'>Go back to task list.</a>";
    exit;
}

// Handle task update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['task_description'], $_POST['status'])) {
        $task_description = $_POST['task_description'];
        $status = $_POST['status'];

        // Update task details
        $sql = "UPDATE tasks SET task_description = ?, status = ? WHERE task_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssi', $task_description, $status, $task_id);
        $stmt->execute();

        // Redirect back to task list after updating
        header("Location: assign_tasks.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <link rel="stylesheet" href="../assets/css/admin_styles.css"> <!-- Link to your admin_styles.css -->
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
        }

        form {
            margin-bottom: 40px;
        }

        textarea, select, input[type="text"], button {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        button {
            background-color: #D62828;
            color: white;
            font-weight: bold;
            margin-top: 20px;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        button:hover {
            background-color: #a61717;
        }

        .back-button-container {
            text-align: center;
            margin-top: 30px;
        }

        .back-button {
            background-color: #D62828;
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 500;
            text-decoration: none;
        }

        .back-button:hover {
            background-color: #a61717;
        }
    </style>
</head>
<body>

<!-- Logo on top right -->
<div class="top-bar">
    <img src="../assets/images/hcdrd_logo.png" alt="HCDRD Logo">
</div>

<!-- Main content -->
<div class="container">
    <h2>Edit Task</h2>

    <form action="edit_tasks.php?task_id=<?= $task_id ?>" method="POST">
        <label for="task_description">Task Description</label>
        <textarea name="task_description" id="task_description" rows="4"><?= htmlspecialchars($task['task_description']) ?></textarea><br><br>

        <label for="status">Status</label>
        <select name="status" id="status">
            <option value="Pending" <?= $task['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
            <option value="In Progress" <?= $task['status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
            <option value="Done" <?= $task['status'] === 'Done' ? 'selected' : '' ?>>Done</option>
        </select><br><br>

        <button type="submit">Update Task</button>
    </form>

    <div class="back-button-container">
        <a href="assign_tasks.php" class="back-button">← Back to Task List</a>
    </div>
</div>

</body>
</html>

<?php
$conn->close();
?>