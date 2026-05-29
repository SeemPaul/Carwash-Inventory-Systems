<?php
include 'auth.php';
include 'role_check.php';
require_role(['Admin', 'Manager', 'Owner']);
include 'config.php';

if (!isset($_GET['id'])) {
    die("Product ID is missing.");
}

$product_id = (int)$_GET['id'];

$product_stmt = $conn->prepare("
    SELECT 
        p.product_id,
        p.product_name,
        p.unit,
        p.quantity_in_stock,
        p.reorder_level,
        p.status,
        c.category_name,
        s.supplier_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
    LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id
    WHERE p.product_id = ?
");
$product_stmt->bind_param("i", $product_id);
$product_stmt->execute();
$product_result = $product_stmt->get_result();

if (!$product_result || $product_result->num_rows === 0) {
    die("Product not found.");
}

$product = $product_result->fetch_assoc();
$product_stmt->close();

$history_stmt = $conn->prepare("
    SELECT 
        log_type,
        quantity,
        remarks,
        date_logged
    FROM inventory_logs
    WHERE product_id = ?
    ORDER BY date_logged DESC
");
$history_stmt->bind_param("i", $product_id);
$history_stmt->execute();
$history_result = $history_stmt->get_result();

include 'header.php';
?>

<div class="main-wrapper">
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <h1 class="page-title">Product Transaction History</h1>

        <?php include 'breadcrumb.php'; ?>

        <div class="page-card">
            <a class="action-btn" href="inventory.php">Back to Inventory</a>
            <a class="action-btn" href="inventory_logs.php">Inventory History</a>
            <a class="action-btn" href="dashboard.php">Dashboard</a>
        </div>

        <div class="page-card">
            <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>

            <table>
                <tr>
                    <th>Category</th>
                    <th>Supplier</th>
                    <th>Unit</th>
                    <th>Current Stock</th>
                    <th>Reorder Level</th>
                    <th>Status</th>
                </tr>
                <tr>
                    <td><?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($product['supplier_name'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($product['unit']); ?></td>
                    <td><?php echo (int)$product['quantity_in_stock']; ?></td>
                    <td><?php echo (int)$product['reorder_level']; ?></td>
                    <td><?php echo htmlspecialchars($product['status']); ?></td>
                </tr>
            </table>
        </div>

        <div class="page-card">
            <h3>Transaction History</h3>

            <?php if ($history_result && $history_result->num_rows > 0): ?>
                <table>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Remarks</th>
                    </tr>

                    <?php while ($row = $history_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['date_logged']); ?></td>
                            <td class="<?php echo $row['log_type'] === 'IN' ? 'success-text' : 'low-stock-text'; ?>">
                                <?php echo htmlspecialchars($row['log_type']); ?>
                            </td>
                            <td>
                                <?php echo $row['log_type'] === 'IN' ? '+' : '-'; ?><?php echo (int)$row['quantity']; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['remarks']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p class="empty-message">No transaction history found for this product.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>