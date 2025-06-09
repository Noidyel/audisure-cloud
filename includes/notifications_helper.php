<?php
function addNotification($conn, $user_email, $role, $message, $link = null) {
    $stmt = $conn->prepare("INSERT INTO notifications (user_email, role, message, link) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $user_email, $role, $message, $link);
    $stmt->execute();
}
