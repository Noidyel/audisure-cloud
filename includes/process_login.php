<?php
session_start();
require '../includes/db.php'; // assumes $pdo for PostgreSQL PDO connection

// Clear old errors
unset($_SESSION['error']);

if (isset($_POST['email'], $_POST['password'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if (password_verify($password, $user['password'])) {
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_status'] = $user['status'] ?? 'active';
            $_SESSION['role'] = strtolower($user['role'] ?? 'user');
            $_SESSION['first_name'] = $user['first_name'] ?? 'User';

            // Redirect by role
            switch ($_SESSION['role']) {
                case 'admin':
                    header("Location: ../admin/admin_dashboard.php");
                    break;
                case 'user':
                    header("Location: ../users/user_dashboard.php");
                    break;
                case 'applicant':
                    header("Location: ../mobile/applicant_dashboard.php");
                    break;
                default:
                    $_SESSION['error'] = "Unknown user role: " . htmlspecialchars($_SESSION['role']);
                    header("Location: ../login.php");
                    break;
            }
            exit();
        } else {
            $_SESSION['error'] = "Incorrect password.";
        }
    } else {
        $_SESSION['error'] = "No account found with that email.";
    }
} else {
    $_SESSION['error'] = "Both email and password are required.";
}

header("Location: ../login.php");
exit();
