<?php
include 'cloudinary_config.php';
include '../includes/db.php'; // Make sure this connects to your Audisure DB

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    try {
        $originalName = pathinfo($_FILES['file']['name'], PATHINFO_FILENAME);
        $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

        $customName = $originalName . '_' . time();

        $upload = $cloudinary->uploadApi()->upload(
            $_FILES['file']['tmp_name'],
            [
                'resource_type' => 'raw',
                'public_id' => $customName,
                'format' => $extension
            ]
        );

        // Get current user ID from session
        session_start();
        $user_id = $_SESSION['user_id'];

        // Insert into the `documents` table
        $stmt = $conn->prepare("INSERT INTO documents (user_id, file_name, file_url, upload_date) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iss", $user_id, $_FILES['file']['name'], $upload['secure_url']);
        $stmt->execute();
        $stmt->close();

        echo "✅ File uploaded!<br>";
        echo "📎 URL: <a href='" . $upload['secure_url'] . "' target='_blank'>" . $upload['secure_url'] . "</a>";

    } catch (Exception $e) {
        echo "❌ Upload failed: " . $e->getMessage();
    }
}
?>
