<?php
// PostgreSQL-compatible db.php using PDO

$host = 'localhost';
$db   = 'audisure';
$user = 'postgres';          // Replace if your pgAdmin shows a different username
$pass = 'Audisure01!';       // Update if you change your password

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully";
} catch (PDOException $e) {
    die("PostgreSQL connection failed: " . $e->getMessage());
}
?>
