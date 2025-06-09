<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .login-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .logo {
            width: 80px;
            margin-bottom: 20px;
        }

        h2 {
            color: #222;
            font-size: 22px;
            margin-bottom: 24px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        input[type="email"],
        input[type="password"] {
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
            width: 100%;
        }

        input:focus {
            outline: none;
            border-color: #c62828;
        }

        button {
            background-color: #c62828;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }

        button:hover {
            background-color: #a61717;
        }

        p {
            margin-top: 12px;
            font-size: 14px;
        }

        a {
            color: #c62828;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .error-message {
            background-color: #ffebee;
            color: #b71c1c;
            padding: 12px;
            margin-bottom: 20px;
            border-left: 4px solid #e53935;
            border-radius: 6px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="assets/images/hcdrd_logo.png" alt="HCDRD Logo" class="logo">
        <h2>Login</h2>

        <?php
        if (isset($_SESSION['error'])) {
            echo "<p class='error-message'>" . $_SESSION['error'] . "</p>";
            unset($_SESSION['error']);
        }
        ?>

        <form action="process/process_login.php" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>
