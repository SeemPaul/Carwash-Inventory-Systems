<?php
include 'auth.php';
include 'role_check.php';
require_role(['Admin', 'Manager', 'Owner']);
include 'config.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    $stmt = $conn->prepare("DELETE FROM service_products WHERE service_product_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: service_products.php");
        exit();
    } else {
        echo "Error deleting mapping.";
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}
?>