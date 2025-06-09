<?php
include '../includes/auth_user.php';
include '../includes/db.php';

$user_email = $_SESSION['user_email'] ?? null;
$notification_count = 0;

if ($user_email) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM notifications WHERE user_email = ?");
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $stmt->bind_result($notification_count);
    $stmt->fetch();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: #fefefe;
            padding: 0;
            margin: 0;
        }

        .container {
            max-width: 900px;
            margin: 60px auto;
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            min-height: 70vh;
            position: relative;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .logo {
            height: 60px;
        }

        h2 {
            font-size: 22px;
            font-weight: 600;
            color: #222;
        }

        .notification-bar {
            text-align: right;
            margin-bottom: 30px;
        }

        .notification-bar a {
            background-color: #c62828;
            color: white;
            padding: 10px 18px;
            border-radius: 6px;
            font-size: 15px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.2s ease-in-out;
        }

        .notification-bar a:hover {
            background-color: #a61717;
        }

        nav {
            display: flex;
            flex-direction: column;
            gap: 15px;
            flex-grow: 1;
        }

        nav a.nav-btn {
            text-decoration: none;
            padding: 14px 20px;
            background-color: #fff;
            border: 2px solid #c62828;
            color: #c62828;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
        }

        nav a.nav-btn:hover {
            background-color: #fbeaea;
            border-color: #a61717;
            color: #a61717;
        }

        .logout-container {
            margin-top: auto;
            display: flex;
            justify-content: flex-end;
            padding-top: 30px;
        }

        .logout-container a {
            background-color: #a61717;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.2s ease-in-out;
        }

        .logout-container a:hover {
            background-color: #7f0e0e;
        }

        @media (max-width: 600px) {
            .container {
                padding: 25px;
            }

            .logo {
                height: 50px;
            }

            h2 {
                font-size: 20px;
            }

            nav a {
                font-size: 15px;
                padding: 12px;
            }

            .logout-container {
                justify-content: center;
            }

            .top-bar {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .notification-bar {
                width: 100%;
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Top Bar: Welcome & Logo -->
        <div class="top-bar">
            <h2>Welcome, <?= htmlspecialchars($_SESSION['first_name'] ?? 'User') ?> (User)</h2>
            <img src="../assets/images/hcdrd_logo.png" alt="HCDRD Logo" class="logo">
        </div>

        <!-- Notification Bar -->
        <div class="notification-bar">
            <a href="user_notifications.php">🔔 Notifications (<?= $notification_count ?>)</a>
        </div>

        <!-- Navigation Menu -->
        <nav>
            <a href="upload_document.php" class="nav-btn">📤 Upload Document</a>
            <a href="todo_list.php" class="nav-btn">📝 To-Do List</a>
            <a href="view_status.php" class="nav-btn">📊 Track Verification Progress</a>
        </nav>

        <!-- Logout Bottom Right -->
        <div class="logout-container">
            <a href="../logout.php">🚪 Logout</a>
        </div>
    </div>
</body>
</html>
