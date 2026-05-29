<?php
include 'auth.php';
include 'role_check.php';
require_role(['Admin', 'Manager', 'Owner']);
include 'config.php';

$sql = "SELECT * FROM suppliers
        WHERE deleted_status = 'Active'
        ORDER BY supplier_name ASC";$result = $conn->query($sql);

include 'header.php';
?>

<div class="main-wrapper">
    <?php include 'sidebar.php'; ?>

    <div class="content">
    <h1 class="page-title">Suppliers List</h1>
        <?php include 'breadcrumb.php'; ?>

    <div class="page-card">
        <a class="action-btn" href="add_supplier.php">Add Supplier</a>
        <a class="action-btn" href="add_product.php">Add Product</a>
        <a class="action-btn" href="inventory.php">View Inventory</a>
        <a class="action-btn" href="dashboard.php">Dashboard</a>
    </div>

    <div class="page-card">
        <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Supplier Name</th>
                    <th>Contact Person</th>
                    <th>Phone Number</th>
                    <th>Actions</th>
                </tr>

                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['supplier_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['contact_person']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td>
                            <div class="action-group">
                                    <a class="btn-sm btn-edit" href="edit_supplier.php?id=<?php echo $row['supplier_id']; ?>">Edit</a>
                                    <a class="btn-sm btn-deactivate" href="delete_supplier.php?id=<?php echo $row['supplier_id']; ?>" onclick="return confirm('Deactivate this supplier?')">Deactivate</a>
                                    <a class="btn-sm btn-delete" href="delete_supplier_record.php?id=<?php echo $row['supplier_id']; ?>" onclick="return confirm('Move this supplier to deleted history?')">Delete</a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p class="empty-message">No suppliers found yet.</p>
        <?php endif; ?>
    </div>

<?php include 'footer.php'; ?>