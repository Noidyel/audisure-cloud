<?php
function addNotification($conn, $user_email, $role, $message, $link = null) {
    $sql = "INSERT INTO notifications (user_email, role, message, link) VALUES ($1, $2, $3, $4)";
    $result = pg_query_params($conn, $sql, [$user_email, $role, $message, $link]);

    if (!$result) {
        error_log("Failed to insert notification: " . pg_last_error($conn));
    }
}
