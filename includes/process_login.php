<?php
session_start();
include('../includes/db.php');

// Clear any old session error
unset($_SESSION['error']);

// Check if email and password are submitted
if (isset($_POST['email'], $_POST['password'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user with the given email exists
    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_status'] = $user['status'] ?? 'active';
            $_SESSION['role'] = strtolower($user['role'] ?? 'user'); // default to 'user' if missing
            $_SESSION['first_name'] = $user['first_name'] ?? 'User';

            // Redirect based on role
            switch ($_SESSION['role']) {
                case 'admin':
                    header("Location: ../admin/admin_dashboard.php");
                    break;
                case 'user':
                    header("Location: ../users/user_dashboard.php");
                    break;
                case 'applicant':
                    header("Location: ../mobile/applicant_dashboard.php"); // Assuming an applicant dashboard exists
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

$conn->close();
header("Location: ../login.php");
exit();
?>
    