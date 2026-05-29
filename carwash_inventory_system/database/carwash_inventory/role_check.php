<?php
function require_role($allowed_roles = []) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['role'])) {
        header("Location: login.php");
        exit();
    }

    if (!in_array($_SESSION['role'], $allowed_roles)) {
        header("Location: unauthorized.php");
        exit();
    }
}
?>