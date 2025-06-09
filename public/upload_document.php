<?php
include '../includes/auth_user.php';
include '../includes/db.php';
include '../cloudinary/cloudinary_config.php';

$message = '';
$uploaded = false;

// Generate random UID
function generateRandomUID($length = 9) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $uid = '';
    for ($i = 0; $i < $length; $i++) {
        $uid .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $uid;
}

// Check UID uniqueness
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
        $message = "❌ Sorry, only PDF, DOC, and DOCX files are allowed.";
    } else {
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

            $stmt = $conn->prepare("INSERT INTO documents (user_id, title, file_path, file_type, document_uid) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", $_SESSION['user_id'], $originalName, $file_url, $fileType, $document_uid);

            if ($stmt->execute()) {
                $status_stmt = $conn->prepare("INSERT INTO documents_statuses (document_uid, status, changed_by) VALUES (?, 'pending', ?)");
                $status_stmt->bind_param("si", $document_uid, $_SESSION['user_id']);

                if ($status_stmt->execute()) {
                    $message = "✅ <strong>" . htmlspecialchars($originalName) . "</strong> uploaded successfully.<br>📎 URL: <a href='" . $file_url . "' target='_blank'>" . $file_url . "</a><br>📄 Document UID: <strong>$document_uid</strong>";
                    $uploaded = true;
                } else {
                    $message = "⚠️ File uploaded, but status saving failed.";
                }
                $status_stmt->close();
            } else {
                $message = "⚠️ File uploaded, but saving to the database failed.";
            }
            $stmt->close();
        } catch (Exception $e) {
            $message = "❌ Upload failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Document</title>
    <link rel="stylesheet" href="../assets/css/users_styles.css">
    <style>
        .progress-container {
            display: none;
            margin-top: 10px;
        }

        .progress-bar {
            width: 0%;
            height: 20px;
            background-color: #dc3545;
            transition: width 0.3s;
        }

        .upload-message {
            background: #f1f1f1;
            padding: 15px;
            border-left: 5px solid #dc3545;
            margin-top: 20px;
            word-wrap: break-word;
            overflow-wrap: break-word;
            max-width: 100%;
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <img src="../assets/images/hcdrd_logo.png" alt="HCDRD Logo" class="logo">
    </header>

    <h2>Upload Document</h2>
    <p>📝 Allowed file types: <strong>.pdf, .doc, .docx</strong></p>

    <?php if (!empty($message)): ?>
        <div class="upload-message"><?php echo $message; ?></div>
    <?php endif; ?>

    <!-- Upload Progress -->
    <div id="uploadProgressContainer" class="progress-container">
        <div class="progress-bar" id="uploadProgressBar"></div>
        <p id="uploadStatus">Uploading...</p>
    </div>

    <!-- Upload Form -->
    <form method="POST" enctype="multipart/form-data" id="uploadForm" <?php if ($uploaded) echo 'style="display: none;"'; ?>>
        <input type="file" name="document" id="fileInput" required>
        <br><br>
        <button type="submit" id="submitBtn">Upload Document</button>
    </form>

    <!-- Back to Dashboard Button -->
    <a href="user_dashboard.php" class="back-link">← Back to Dashboard</a>
</div>

<script>
    const fileInput = document.getElementById('fileInput');
    const uploadForm = document.getElementById('uploadForm');
    const uploadProgressContainer = document.getElementById('uploadProgressContainer');
    const uploadProgressBar = document.getElementById('uploadProgressBar');
    const submitBtn = document.getElementById('submitBtn');

    fileInput.addEventListener('change', () => {
        const allowedExtensions = ['pdf', 'doc', 'docx'];
        const file = fileInput.files[0];
        const extension = file.name.split('.').pop().toLowerCase();

        if (!allowedExtensions.includes(extension)) {
            alert('❌ Only PDF, DOC, and DOCX files are allowed.');
            fileInput.value = '';
            submitBtn.style.display = 'none';
        } else {
            submitBtn.style.display = 'inline-block';
        }
    });

    uploadForm.addEventListener('submit', function (e) {
        const file = fileInput.files[0];
        if (!file) {
            alert("Please select a file first.");
            e.preventDefault();
            return;
        }

        // Show progress bar and hide submit button
        uploadProgressContainer.style.display = 'block';
        submitBtn.style.display = 'none';

        let width = 0;
        const progressInterval = setInterval(() => {
            if (width >= 100) {
                clearInterval(progressInterval);
            } else {
                width += 10;
                uploadProgressBar.style.width = width + '%';
            }
        }, 300);
    });
</script>
</body>
</html>
