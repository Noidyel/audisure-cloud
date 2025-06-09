<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "audisure_db");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$first_name = $conn->real_escape_string($_POST['first_name']);
$last_name = $conn->real_escape_string($_POST['last_name']);
$email = $conn->real_escape_string($_POST['email']);
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Encrypt password
$role = 'user'; // Default role is 'user'

// Insert into database
$sql = "INSERT INTO users (first_name, last_name, email, password, role) 
        VALUES ('$first_name', '$last_name', '$email', '$password', '$role')";

if ($conn->query($sql) === TRUE) {
    // Success message
    echo "<script>
            alert('Registration successful!');
            setTimeout(function() {
                window.location.href = '../login.php'; // Redirect to login page after 1 second
            }, 1000); // 1 second delay
          </script>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
