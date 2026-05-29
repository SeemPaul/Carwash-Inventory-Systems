<?php
include 'auth.php';
include 'role_check.php';
require_role(['Admin', 'Manager', 'Owner']);
include 'config.php';
include 'audit_helper.php';

$message = "";
$message_type = "success";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $quantity_added = (int)($_POST['quantity_added'] ?? 0);
    $remarks = trim($_POST['remarks'] ?? '');

    if ($product_id <= 0) {
        $message = "Please select a product.";
        $message_type = "error";
    } elseif ($quantity_added <= 0) {
        $message = "Quantity added must be greater than 0.";
        $message_type = "error";
    } else {
        $stmt = $conn->prepare("
            UPDATE products
            SET quantity_in_stock = quantity_in_stock + ?
            WHERE product_id = ?
        ");
        $stmt->bind_param("ii", $quantity_added, $product_id);

        if ($stmt->execute()) {

    // 🔥 ADD THIS PART
    $log_stmt = $conn->prepare("
        INSERT INTO inventory_logs (product_id, log_type, quantity, remarks)
        VALUES (?, 'IN', ?, ?)
    ");
    $log_stmt->bind_param("iis", $product_id, $quantity_added, $remarks);
    $log_stmt->execute();
    $log_stmt->close();

    log_audit(
    $conn,
    'Restocked Product',
    'Stock In',
    'Added ' . $quantity_added . ' stock to product ID: ' . $product_id
);
    $message = "Stock updated successfully!";
    $message_type = "success";
            $message = "Stock updated successfully!";
            $message_type = "success";
        } else {
            $message = "Error updating stock.";
            $message_type = "error";
        }

        $stmt->close();
    }
}

// Fetch products
$products_sql = "
    SELECT product_id, product_name, unit, quantity_in_stock
    FROM products
    WHERE status = 'Active'
    ORDER BY product_name ASC
";
$products = $conn->query($products_sql);

// Recent stock overview
$stock_overview_sql = "
    SELECT product_name, unit, quantity_in_stock, reorder_level
    FROM products
    ORDER BY product_name ASC
    LIMIT 10
";
$stock_overview = $conn->query($stock_overview_sql);

include 'header.php';
?>

<div class="main-wrapper">
    <?php include 'sidebar.php'; ?>

    <div class="content">
    <h1 class="page-title">Stock In / Restock</h1>
        <?php include 'breadcrumb.php'; ?>

    <div class="page-card">
        <a class="action-btn" href="inventory.php">View Inventory</a>
        <a class="action-btn" href="dashboard.php">Dashboard</a>
        <a class="action-btn" href="reports.php">Reports</a>
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

            <label>Quantity to Add</label>
            <input type="number" name="quantity_added" min="1" required>

            <label>Remarks</label>
            <textarea name="remarks" placeholder="Optional note, e.g. Restocked from supplier..."></textarea>

            <button type="submit" class="action-btn" style="border:none; cursor:pointer;">Update Stock</button>
        </form>

        <?php if (!empty($message)): ?>
            <p class="<?php echo $message_type === 'success' ? 'success-text' : 'low-stock-text'; ?>" style="margin-top:15px;">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>
    </div>

    <div class="page-card">
        <h3>Current Stock Overview</h3>

        <?php if ($stock_overview && $stock_overview->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Product Name</th>
                    <th>Unit</th>
                    <th>Quantity in Stock</th>
                    <th>Reorder Level</th>
                    <th>Status</th>
                </tr>

                <?php while ($row = $stock_overview->fetch_assoc()): ?>
                    <?php $isLow = $row['quantity_in_stock'] <= $row['reorder_level']; ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['unit']); ?></td>
                        <td><?php echo (int)$row['quantity_in_stock']; ?></td>
                        <td><?php echo (int)$row['reorder_level']; ?></td>
                        <td class="<?php echo $isLow ? 'low-stock-text' : 'success-text'; ?>">
                            <?php echo $isLow ? 'LOW STOCK' : 'OK'; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p class="empty-message">No products found.</p>
        <?php endif; ?>
    </div>

<?php include 'footer.php'; ?>