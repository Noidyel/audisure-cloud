<?php
header('Content-Type: application/json');

$host = "localhost";
$user = "root";
$password = "";
$database = "audisure_db";

// Connect to database
$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database connection failed."]);
    exit;
}

$document_uid = $_GET['document_uid'] ?? '';

if (empty($document_uid)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing document_uid parameter."]);
    exit;
}

// Updated query to get the most recent status
$stmt = $conn->prepare("
    SELECT status 
    FROM documents_statuses
    WHERE document_uid = ? 
    ORDER BY updated_at DESC 
    LIMIT 1
");

$stmt->bind_param("s", $document_uid);
$stmt->execute();
$stmt->bind_result($status);

if ($stmt->fetch()) {
    echo json_encode(["status" => $status]);
} else {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "Document not found."]);
}

$stmt->close();
$conn->close();
?>