<?php
include 'auth.php';
include 'role_check.php';
require_role(['Admin', 'Manager', 'Owner']);
include 'config.php';

$deleted_products = $conn->query("
    SELECT product_id, product_name, unit, quantity_in_stock, deleted_status
    FROM products
    WHERE deleted_status = 'Deleted'
    ORDER BY product_name ASC
");

$deleted_services = $conn->query("
    SELECT service_id, service_name, estimated_duration, deleted_status
    FROM services
    WHERE deleted_status = 'Deleted'
    ORDER BY service_name ASC
");

$deleted_categories = $conn->query("
    SELECT category_id, category_name, description, deleted_status
    FROM categories
    WHERE deleted_status = 'Deleted'
    ORDER BY category_name ASC
");

$deleted_suppliers = $conn->query("
    SELECT supplier_id, supplier_name, contact_person, phone, deleted_status
    FROM suppliers
    WHERE deleted_status = 'Deleted'
    ORDER BY supplier_name ASC
");

include 'header.php';
?>

<div class="main-wrapper">
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <h1 class="page-title">Deleted Records History</h1>

        <?php include 'breadcrumb.php'; ?>

        <div class="page-card">
            <a class="action-btn" href="dashboard.php">Dashboard</a>
            <a class="action-btn" href="inactive_records.php">Inactive Records</a>
            <a class="action-btn" href="audit_logs.php">Audit Logs</a>
        </div>

        <div class="page-card">
            <h3>Deleted Products</h3>

            <?php if ($deleted_products && $deleted_products->num_rows > 0): ?>
                <table>
                    <tr>
                        <th>Product</th>
                        <th>Unit</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>

                    <?php while ($row = $deleted_products->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['unit']); ?></td>
                            <td><?php echo (int)$row['quantity_in_stock']; ?></td>
                            <td><?php echo htmlspecialchars($row['deleted_status']); ?></td>
                            <td>
                                <a class="action-btn" href="restore_deleted_product.php?id=<?php echo $row['product_id']; ?>" onclick="return confirm('Restore this product from deleted history?')">Restore</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>

                <div class="empty-state">
    <i class="fa-solid fa-trash-can"></i>
    <h3>No deleted products found.</h3>
</div>
            <?php endif; ?>
        </div>

        <div class="page-card">
            <h3>Deleted Services</h3>

            <?php if ($deleted_services && $deleted_services->num_rows > 0): ?>
                <table>
                    <tr>
                        <th>Service</th>
                        <th>Estimated Duration</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>

                    <?php while ($row = $deleted_services->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['estimated_duration']); ?></td>
                            <td><?php echo htmlspecialchars($row['deleted_status']); ?></td>
                            <td>
                                <a class="action-btn" href="restore_deleted_service.php?id=<?php echo $row['service_id']; ?>" onclick="return confirm('Restore this service from deleted history?')">Restore</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <div class="empty-state">
    <i class="fa-solid fa-trash-can"></i>
    <h3>No deleted services found.</h3>
</div>
            <?php endif; ?>
        </div>

        <div class="page-card">
            <h3>Deleted Categories</h3>

            <?php if ($deleted_categories && $deleted_categories->num_rows > 0): ?>
                <table>
                    <tr>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>

                    <?php while ($row = $deleted_categories->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['description'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['deleted_status']); ?></td>
                            <td>
                                <a class="action-btn" href="restore_deleted_category.php?id=<?php echo $row['category_id']; ?>" onclick="return confirm('Restore this category from deleted history?')">Restore</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <div class="empty-state">
    <i class="fa-solid fa-trash-can"></i>
    <h3>No deleted categories found.</h3>
</div>
            <?php endif; ?>
        </div>

        <div class="page-card">
            <h3>Deleted Suppliers</h3>

            <?php if ($deleted_suppliers && $deleted_suppliers->num_rows > 0): ?>
                <table>
                    <tr>
                        <th>Supplier</th>
                        <th>Contact Person</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>

                    <?php while ($row = $deleted_suppliers->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['supplier_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['contact_person'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['phone'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['deleted_status']); ?></td>
                            <td>
                                <a class="action-btn" href="restore_deleted_supplier.php?id=<?php echo $row['supplier_id']; ?>" onclick="return confirm('Restore this supplier from deleted history?')">Restore</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <div class="empty-state">
    <i class="fa-solid fa-trash-can"></i>
    <h3>No deleted suppliers found.</h3>
</div>
               
            <?php endif; ?>
        </div>
 
<?php include 'footer.php'; ?>