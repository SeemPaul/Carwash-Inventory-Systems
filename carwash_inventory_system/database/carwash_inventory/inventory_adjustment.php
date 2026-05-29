<?php
include 'auth.php';
include 'role_check.php';
require_role(['Admin', 'Manager', 'Owner']);
include 'config.php';
include 'audit_helper.php';

$message = "";
$error = "";

$products = $conn->query("
    SELECT product_id, product_name, unit, quantity_in_stock
    FROM products
    WHERE status = 'Active'
    ORDER BY product_name ASC
");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $adjustment_type = $_POST['adjustment_type'] ?? '';
    $quantity = (int)($_POST['quantity'] ?? 0);
    $reason = trim($_POST['reason'] ?? '');

    if ($product_id <= 0) {
        $error = "Please select a product.";
    } elseif (!in_array($adjustment_type, ['ADD', 'DEDUCT'])) {
        $error = "Please select a valid adjustment type.";
    } elseif ($quantity <= 0) {
        $error = "Quantity must be greater than 0.";
    } elseif (empty($reason)) {
        $error = "Please provide a reason for the adjustment.";
    } else {
        $conn->begin_transaction();

        try {
            $stock_stmt = $conn->prepare("SELECT quantity_in_stock FROM products WHERE product_id = ? FOR UPDATE");
            $stock_stmt->bind_param("i", $product_id);
            $stock_stmt->execute();
            $stock_result = $stock_stmt->get_result();
            $product = $stock_result->fetch_assoc();
            $stock_stmt->close();

            if (!$product) {
                throw new Exception("Product not found.");
            }

            $current_stock = (int)$product['quantity_in_stock'];

            if ($adjustment_type === 'DEDUCT' && $quantity > $current_stock) {
                throw new Exception("Cannot deduct more than current stock.");
            }

            if ($adjustment_type === 'ADD') {
                $update_stmt = $conn->prepare("
                    UPDATE products 
                    SET quantity_in_stock = quantity_in_stock + ?
                    WHERE product_id = ?
                ");
                $log_type = 'IN';
                $remarks = "Manual adjustment added: " . $reason;
            } else {
                $update_stmt = $conn->prepare("
                    UPDATE products 
                    SET quantity_in_stock = quantity_in_stock - ?
                    WHERE product_id = ?
                ");
                $log_type = 'OUT';
                $remarks = "Manual adjustment deducted: " . $reason;
            }

            $update_stmt->bind_param("ii", $quantity, $product_id);
            $update_stmt->execute();
            $update_stmt->close();

            $log_stmt = $conn->prepare("
                INSERT INTO inventory_logs (product_id, log_type, quantity, remarks)
                VALUES (?, ?, ?, ?)
            ");
            $log_stmt->bind_param("isis", $product_id, $log_type, $quantity, $remarks);
            $log_stmt->execute();
            $log_stmt->close();

            $conn->commit();

log_audit(
    $conn,
    'Adjusted Inventory',
    'Inventory Adjustment',
    $remarks
);

$message = "Inventory adjustment saved successfully.";
        } catch (Exception $e) {
            $conn->rollback();
            $error = $e->getMessage();
        }
    }
}

include 'header.php';
?>

<div class="main-wrapper">
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <h1 class="page-title">Inventory Adjustment</h1>

        <?php include 'breadcrumb.php'; ?>

        <div class="page-card">
            <a class="action-btn" href="inventory.php">Inventory</a>
            <a class="action-btn" href="inventory_logs.php">Inventory History</a>
            <a class="action-btn" href="dashboard.php">Dashboard</a>
        </div>

        <div class="page-card">
            <form method="POST">
                <label>Select Product</label>
                <select name="product_id" required>
                    <option value="">Select Product</option>
                    <?php while ($row = $products->fetch_assoc()): ?>
                        <option value="<?php echo $row['product_id']; ?>">
                            <?php echo htmlspecialchars($row['product_name'] . " (" . $row['unit'] . ") - Current Stock: " . $row['quantity_in_stock']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label>Adjustment Type</label>
                <select name="adjustment_type" required>
                    <option value="">Select Type</option>
                    <option value="ADD">Add Stock</option>
                    <option value="DEDUCT">Deduct Stock</option>
                </select>

                <label>Quantity</label>
                <input type="number" name="quantity" min="1" required>

                <label>Reason</label>
                <textarea name="reason" placeholder="Example: damaged item, wrong count, missing stock..." required></textarea>

                <button type="submit" class="action-btn">Save Adjustment</button>
            </form>

            <?php if (!empty($message)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
        </div>

<?php include 'footer.php'; ?>