<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HCDRD Document Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: #f9f9f9;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .index-container {
            text-align: center;
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            width: 100%;
            max-width: 480px;
        }

        .logo {
            width: 80px;
            height: auto;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 22px;
            color: #222;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .btn-container {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .login-btn, .register-btn {
            padding: 12px 24px;
            text-decoration: none;
            font-weight: 600;
            border-radius: 6px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .login-btn {
            background-color: #0057a0;
            color: white;
        }

        .register-btn {
            background-color: #e0e0e0;
            color: #333;
        }

        .login-btn:hover {
            background-color: #004080;
            transform: translateY(-2px);
        }

        .register-btn:hover {
            background-color: #c7c7c7;
            transform: translateY(-2px);
        }

        @media (max-width: 480px) {
            .btn-container {
                flex-direction: column;
            }

            .btn-container a {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="index-container">
        <header>
            <img src="assets/images/hcdrd_logo.png" alt="HCDRD Logo" class="logo">
        </header>

        <h1>HCDRD Document Portal</h1>
        
        <div class="btn-container">
            <a href="login.php" class="login-btn">Login</a>
            <a href="register.php" class="register-btn">Register</a>
        </div>
    </div>
</body>
</html>
