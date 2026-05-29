<?php
include 'auth.php';
include 'role_check.php';
require_role(['Admin', 'Manager', 'Owner']);
include 'config.php';
include 'audit_helper.php';

if (!isset($_GET['id'])) {
    die("Product ID is missing.");
}

$id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    die("Product not found.");
}

$product = $result->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['product_name']);
    $stock = (int)$_POST['quantity_in_stock'];
    $reorder = (int)$_POST['reorder_level'];

    $stmt = $conn->prepare("
        UPDATE products 
        SET product_name = ?, quantity_in_stock = ?, reorder_level = ?
        WHERE product_id = ?
    ");
    $stmt->bind_param("siii", $name, $stock, $reorder, $id);

    if ($stmt->execute()) {

    log_audit(
        $conn,
        'Edited Product',
        'Products',
        'Updated product: ' . $name
    );

    header("Location: inventory.php");
    exit();
} else {
        $error = "Update failed.";
    }

    $stmt->close();
}


include 'header.php';
?>

<div class="main-wrapper">
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <h1 class="page-title">Edit Product</h1>

        <?php include 'breadcrumb.php'; ?>

        <div class="page-card">
            <a class="action-btn" href="inventory.php">Back to Inventory</a>
            <a class="action-btn" href="dashboard.php">Dashboard</a>
        </div>

        <div class="page-card">
            <form method="POST">
                <label>Product Name</label>
                <input type="text" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>

                <label>Stock</label>
                <input type="number" name="quantity_in_stock" value="<?php echo (int)$product['quantity_in_stock']; ?>" required>

                <label>Reorder Level</label>
                <input type="number" name="reorder_level" value="<?php echo (int)$product['reorder_level']; ?>" required>

                <button type="submit" class="action-btn">Update Product</button>
            </form>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
        </div>

<?php include 'footer.php'; ?>