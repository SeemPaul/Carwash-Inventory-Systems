<?php
include 'auth.php';
include 'role_check.php';
require_role(['Admin', 'Manager', 'Owner']);
include 'config.php';
include 'audit_helper.php';

if (!isset($_GET['id'])) {
    die("Service ID is missing.");
}

$service_id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT service_name FROM services WHERE service_id = ?");
$stmt->bind_param("i", $service_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    die("Service not found.");
}

$service = $result->fetch_assoc();
$service_name = $service['service_name'];
$stmt->close();

$stmt = $conn->prepare("UPDATE services SET status = 'Active' WHERE service_id = ?");
$stmt->bind_param("i", $service_id);

if ($stmt->execute()) {
    log_audit(
        $conn,
        'Reactivated Service',
        'Services',
        'Set service as Active: ' . $service_name
    );

    header("Location: inactive_records.php");
    exit();
} else {
    die("Failed to reactivate service.");
}