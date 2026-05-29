<?php
include 'auth.php';
include 'role_check.php';
require_role(['Admin', 'Manager', 'Owner']);
include 'config.php';

$sql = "
    SELECT 
        p.product_id,
        p.product_name,
        p.unit,
        p.quantity_in_stock,
        p.reorder_level,
        s.supplier_name,
        c.category_name
    FROM products p
    LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id
    LEFT JOIN categories c ON p.category_id = c.category_id
    WHERE p.quantity_in_stock <= p.reorder_level
    ORDER BY p.quantity_in_stock ASC
";

$result = $conn->query($sql);

include 'header.php';
?>

<div class="main-wrapper">
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <h1 class="page-title">Reorder List</h1>

        <?php include 'breadcrumb.php'; ?>

        <div class="page-card">
            <a class="action-btn" href="inventory.php">Inventory</a>
            <a class="action-btn" href="stock_in.php">Stock In / Restock</a>
            <a class="action-btn" href="reports.php">Reports</a>
        </div>

        <div class="page-card">
            <?php if ($result && $result->num_rows > 0): ?>
                <table>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Supplier</th>
                        <th>Unit</th>
                        <th>Current Stock</th>
                        <th>Reorder Level</th>
                        <th>Suggested Action</th>
                    </tr>

                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['category_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['supplier_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['unit']); ?></td>
                            <td class="low-stock-text"><?php echo (int)$row['quantity_in_stock']; ?></td>
                            <td><?php echo (int)$row['reorder_level']; ?></td>
                            <td>
                                <a href="stock_in.php" class="action-btn">Restock</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p class="empty-message">No products currently need restocking.</p>
            <?php endif; ?>
        </div>

<?php include 'footer.php'; ?>