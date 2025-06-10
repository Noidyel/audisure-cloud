<?php
include '../includes/auth_user.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Document</title>
    <link rel="stylesheet" href="../assets/css/user_styles.css">
</head>
<body>
<div class="container">
    <header>
        <img src="../assets/images/hcdrd_logo.png" alt="HCDRD Logo" class="logo">
    </header>

    <h2>Upload Document</h2>
    <p>📝 Allowed file types: <strong>.pdf, .doc, .docx</strong></p>

    <!-- Upload Progress -->
    <div id="uploadProgressContainer" class="progress-container">
        <div class="progress-bar" id="uploadProgressBar"></div>
        <p id="uploadStatus">Uploading...</p>
    </div>

    <!-- Upload Form -->
    <form id="uploadForm">
        <input type="file" name="document" id="fileInput" required>
        <br><br>
        <button type="submit" id="submitBtn">Upload Document</button>
    </form>

    <!-- Success/Error Message -->
    <div id="uploadResult"></div>

    <!-- Back to Dashboard Button -->
    <a href="user_dashboard.php" class="back-link">← Back to Dashboard</a>
</div>

<script>
    const uploadForm = document.getElementById('uploadForm');
    const fileInput = document.getElementById('fileInput');
    const uploadProgressContainer = document.getElementById('uploadProgressContainer');
    const uploadProgressBar = document.getElementById('uploadProgressBar');
    const submitBtn = document.getElementById('submitBtn');
    const uploadResult = document.getElementById('uploadResult');

    uploadForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const file = fileInput.files[0];
        if (!file) return alert("Please select a file.");

        const allowedExtensions = ['pdf', 'doc', 'docx'];
        const extension = file.name.split('.').pop().toLowerCase();

        if (!allowedExtensions.includes(extension)) {
            alert('❌ Only PDF, DOC, and DOCX files are allowed.');
            return;
        }

        const formData = new FormData();
        formData.append('document', file);

        uploadProgressContainer.style.display = 'block';
        uploadProgressBar.style.width = '0%';
        submitBtn.disabled = true;

        // Fake progress simulation
        let width = 0;
        const progressInterval = setInterval(() => {
            width += 10;
            if (width <= 90) {
                uploadProgressBar.style.width = width + '%';
            } else {
                clearInterval(progressInterval);
            }
        }, 300);

        fetch('../includes/process_upload.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            clearInterval(progressInterval);
            uploadProgressBar.style.width = '100%';

            uploadResult.innerHTML = `<div class="upload-message">${data.message}</div>`;

            if (data.success) {
                uploadForm.style.display = 'none';
            }

            submitBtn.disabled = false;
        })
        .catch(err => {
            clearInterval(progressInterval);
            uploadProgressBar.style.width = '100%';
            uploadResult.innerHTML = '<div class="upload-message">❌ Upload failed. Please try again.</div>';
            submitBtn.disabled = false;
        });
    });
</script>
</body>
</html>
