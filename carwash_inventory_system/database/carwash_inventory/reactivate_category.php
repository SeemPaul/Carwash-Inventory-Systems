<?php
include 'auth.php';
include 'role_check.php';
require_role(['Admin', 'Manager', 'Owner']);
include 'config.php';
include 'audit_helper.php';

if (!isset($_GET['id'])) {
    die("Category ID is missing.");
}

$category_id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT category_name FROM categories WHERE category_id = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    die("Category not found.");
}

$category = $result->fetch_assoc();
$category_name = $category['category_name'];
$stmt->close();

$stmt = $conn->prepare("UPDATE categories SET status = 'Active' WHERE category_id = ?");
$stmt->bind_param("i", $category_id);

if ($stmt->execute()) {
    log_audit(
        $conn,
        'Reactivated Category',
        'Categories',
        'Set category as Active: ' . $category_name
    );

    header("Location: inactive_records.php");
    exit();
} else {
    die("Failed to reactivate category.");
}