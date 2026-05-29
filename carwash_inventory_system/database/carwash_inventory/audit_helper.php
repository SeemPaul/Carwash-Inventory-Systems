<?php
function log_audit($conn, $action, $module, $description) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $user_id = $_SESSION['user_id'] ?? null;

    $stmt = $conn->prepare("
        INSERT INTO audit_logs (user_id, action, module, description)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->bind_param("isss", $user_id, $action, $module, $description);
    $stmt->execute();
    $stmt->close();
}
?>