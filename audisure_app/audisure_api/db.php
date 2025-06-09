<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
$host = 'localhost';
$db = 'audisure_db'; // Change this to your database name
$user = 'root'; // default user
$pass = ''; // default password

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed']));
}
?>
