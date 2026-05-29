<?php
include 'auth.php';
include 'role_check.php';
require_role(['Admin', 'Manager', 'Owner']);
include 'config.php';

$inactive_products = $conn->query("
    SELECT product_id, product_name, unit, quantity_in_stock, status
    FROM products
    WHERE status = 'Inactive'
    ORDER BY product_name ASC
");

$inactive_services = $conn->query("
    SELECT service_id, service_name, estimated_duration, status
    FROM services
    WHERE status = 'Inactive'
    ORDER BY service_name ASC
");
$inactive_categories = $conn->query("
    SELECT category_id, category_name, description, status
    FROM categories
    WHERE status = 'Inactive'
    ORDER BY category_name ASC
");

$inactive_suppliers = $conn->query("
    SELECT supplier_id, supplier_name, contact_person, phone, status
    FROM suppliers
    WHERE status = 'Inactive'
    ORDER BY supplier_name ASC
");

include 'header.php';
?>

<div class="main-wrapper">
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <h1 class="page-title">Inactive Records</h1>

        <?php include 'breadcrumb.php'; ?>

        <div class="page-card">
            <a class="action-btn" href="inventory.php">Inventory</a>
            <a class="action-btn" href="services.php">Services</a>
            <a class="action-btn" href="audit_logs.php">Audit Logs</a>
        </div>

        <div class="page-card">
            <h3>Inactive Products</h3>

            <?php if ($inactive_products && $inactive_products->num_rows > 0): ?>
                <table>
                    <tr>
                        <th>Product</th>
                        <th>Unit</th>
                        <th>Current Stock</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>

                    <?php while ($row = $inactive_products->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['unit']); ?></td>
                            <td><?php echo (int)$row['quantity_in_stock']; ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td>
                                <a class="action-btn" href="reactivate_product.php?id=<?php echo $row['product_id']; ?>" onclick="return confirm('Reactivate this product?')">Reactivate</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <div class="empty-state">
    <i class="fa-solid fa-box-open"></i>
    <h3>No inactive products found.</h3>
</div>
            <?php endif; ?>
        </div>

        <div class="page-card">
            <h3>Inactive Services</h3>

            <?php if ($inactive_services && $inactive_services->num_rows > 0): ?>
                <table>
                    <tr>
                        <th>Service</th>
                        <th>Estimated Duration</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>

                    <?php while ($row = $inactive_services->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['estimated_duration']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td>
                                <a class="action-btn" href="reactivate_service.php?id=<?php echo $row['service_id']; ?>" onclick="return confirm('Reactivate this service?')">Reactivate</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <div class="empty-state">
    <i class="fa-solid fa-box-open"></i>
    <h3>No inactive services found.</h3>
</div>
            <?php endif; ?>
        </div>

<div class="page-card">
    <h3>Inactive Categories</h3>

    <?php if ($inactive_categories && $inactive_categories->num_rows > 0): ?>
        <table>
            <tr>
                <th>Category</th>
                <th>Description</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php while ($row = $inactive_categories->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['description'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td>
                        <a class="action-btn" href="reactivate_category.php?id=<?php echo $row['category_id']; ?>" onclick="return confirm('Reactivate this category?')">Reactivate</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <div class="empty-state">
    <i class="fa-solid fa-box-open"></i>
    <h3>No inactive categories found.</h3>
</div>
    <?php endif; ?>
</div>

<div class="page-card">
    <h3>Inactive Suppliers</h3>

    <?php if ($inactive_suppliers && $inactive_suppliers->num_rows > 0): ?>
        <table>
            <tr>
                <th>Supplier</th>
                <th>Contact Person</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php while ($row = $inactive_suppliers->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['supplier_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['contact_person'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($row['phone'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td>
                        <a class="action-btn" href="reactivate_supplier.php?id=<?php echo $row['supplier_id']; ?>" onclick="return confirm('Reactivate this supplier?')">Reactivate</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <div class="empty-state">
    <i class="fa-solid fa-box-open"></i>
    <h3>No inactive suppliers found.</h3>
</div>
    
    <?php endif; ?>
</div>
        
<?php include 'footer.php'; ?>