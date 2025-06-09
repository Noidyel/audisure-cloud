<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
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

        .register-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            width: 100%;
            max-width: 440px;
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

        input {
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

        .success-message {
            font-size: 16px;
            color: green;
            margin-top: 16px;
        }
    </style>
    <script>
        function validateForm(event) {
            const form = document.forms["registerForm"];
            const nameRegex = /^[A-Za-z]+$/;
            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

            if (!form["first_name"].value.match(nameRegex) || !form["last_name"].value.match(nameRegex)) {
                alert('First and Last Name must contain only letters.');
                event.preventDefault();
                return false;
            }

            if (!form["password"].value.match(passwordRegex)) {
                alert('Password must be 8+ chars with uppercase, lowercase, number, and special char.');
                event.preventDefault();
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
    <div class="register-container">
        <header>
            <img src="assets/images/hcdrd_logo.png" alt="HCDRD Logo" class="logo">
        </header>

        <h2>Create a User Account</h2>

        <form name="registerForm" action="process/process_register.php" method="POST" onsubmit="return validateForm(event)">
            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" placeholder="Last Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>
