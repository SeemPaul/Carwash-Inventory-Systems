<?php
include 'auth.php';
include 'role_check.php';
require_role(['Admin', 'Manager', 'Owner']);
include 'config.php';
include 'audit_helper.php';

if (!isset($_GET['id'])) {
    die("Supplier ID is missing.");
}

$supplier_id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT supplier_name FROM suppliers WHERE supplier_id = ?");
$stmt->bind_param("i", $supplier_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    die("Supplier not found.");
}

$supplier = $result->fetch_assoc();
$supplier_name = $supplier['supplier_name'];
$stmt->close();

$stmt = $conn->prepare("UPDATE suppliers SET deleted_status = 'Deleted' WHERE supplier_id = ?");
$stmt->bind_param("i", $supplier_id);

if ($stmt->execute()) {

    log_audit(
        $conn,
        'Deleted Supplier',
        'Suppliers',
        'Moved supplier to deleted history: ' . $supplier_name
    );

    header("Location: suppliers.php");
    exit();

} else {
    die("Failed to delete supplier.");
}