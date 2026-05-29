<?php
include 'auth.php';
include 'role_check.php';
require_role(['Admin', 'Manager', 'Owner']);
include 'config.php';
include 'audit_helper.php';

if (!isset($_GET['id'])) {
    die("Product ID is missing.");
}

$product_id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT product_name FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    die("Product not found.");
}

$product = $result->fetch_assoc();
$product_name = $product['product_name'];
$stmt->close();

$stmt = $conn->prepare("UPDATE products SET status = 'Active' WHERE product_id = ?");
$stmt->bind_param("i", $product_id);

if ($stmt->execute()) {
    log_audit(
        $conn,
        'Reactivated Product',
        'Products',
        'Set product as Active: ' . $product_name
    );

    header("Location: inactive_records.php");
    exit();
} else {
    die("Failed to reactivate product.");
}