<?php
session_start();
include '../includes/db.php';
include '../cloudinary/cloudinary_config.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => '',
    'document_uid' => '',
    'file_url' => ''
];

// Helper functions
function generateRandomUID($length = 9) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $uid = '';
    for ($i = 0; $i < $length; $i++) {
        $uid .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $uid;
}

function isUIDUnique($conn, $uid) {
    $stmt = $conn->prepare("SELECT 1 FROM documents WHERE document_uid = ?");
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    $stmt->store_result();
    $unique = $stmt->num_rows === 0;
    $stmt->close();
    return $unique;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document'])) {
    $file_name = basename($_FILES["document"]["name"]);
    $fileType = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (!in_array($fileType, ['pdf', 'doc', 'docx'])) {
        $response['message'] = "❌ Only PDF, DOC, and DOCX files are allowed.";
        echo json_encode($response);
        exit;
    }

    try {
        $originalName = pathinfo($file_name, PATHINFO_FILENAME);
        $extension = pathinfo($file_name, PATHINFO_EXTENSION);
        $customName = $originalName . '_' . time();

        $upload = $cloudinary->uploadApi()->upload(
            $_FILES['document']['tmp_name'],
            [
                'resource_type' => 'raw',
                'public_id' => $customName,
                'format' => $extension
            ]
        );

        $file_url = $upload['secure_url'];

        do {
            $document_uid = generateRandomUID();
        } while (!isUIDUnique($conn, $document_uid));

        // Insert into documents
        $stmt = $conn->prepare("INSERT INTO documents (user_id, title, file_path, file_type, document_uid) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $_SESSION['user_id'], $originalName, $file_url, $fileType, $document_uid);

        if ($stmt->execute()) {
            $status_stmt = $conn->prepare("INSERT INTO document_statuses (document_uid, status, changed_by) VALUES (?, 'pending', ?)");
            $status_stmt->bind_param("si", $document_uid, $_SESSION['user_id']);

            if ($status_stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "✅ {$originalName} uploaded successfully.";
                $response['file_url'] = $file_url;
                $response['document_uid'] = $document_uid;
            } else {
                $response['message'] = "⚠️ File uploaded but status not saved.";
            }
            $status_stmt->close();
        } else {
            $response['message'] = "⚠️ Upload saved to Cloudinary, but not to database.";
        }

        $stmt->close();
    } catch (Exception $e) {
        $response['message'] = "❌ Upload failed: " . $e->getMessage();
    }
} else {
    $response['message'] = "❌ Invalid request.";
}

echo json_encode($response);
