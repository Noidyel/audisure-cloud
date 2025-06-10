<?php
require '../includes/db.php'; // assumes $pdo for PostgreSQL PDO connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = 'applicant'; // your default role

    // Basic validation
    if (!$first_name || !$last_name || !$email || !$password) {
        die('Please fill in all required fields.');
    }

    // Check if email exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        die('Email already registered.');
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$first_name, $last_name, $email, $hashed_password, $role])) {
        echo "<script>
                alert('Registration successful!');
                setTimeout(function() {
                    window.location.href = '../login.php';
                }, 1000);
              </script>";
    } else {
        die('Registration failed. Please try again.');
    }
} else {
    die('Invalid request method.');
}
  