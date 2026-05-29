<?php
include 'auth.php';
include 'role_check.php';
require_role(['Admin', 'Manager', 'Owner']);
include 'config.php';

$sql = "SELECT * FROM categories 
        WHERE deleted_status = 'Active'
        ORDER BY category_name ASC";$result = $conn->query($sql);

include 'header.php';
?>

<div class="main-wrapper">
    <?php include 'sidebar.php'; ?>

    <div class="content">
    <h1 class="page-title">Categories List</h1>
        <?php include 'breadcrumb.php'; ?>

    <div class="page-card">
        <a class="action-btn" href="add_category.php">Add Category</a>
        <a class="action-btn" href="add_product.php">Add Product</a>
        <a class="action-btn" href="inventory.php">Inventory</a>
    </div>

    <div class="page-card">
        <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Category Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>

                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td>
                                 <div class="action-group">
                                 <a class="btn-sm btn-edit" href="edit_category.php?id=<?php echo $row['category_id']; ?>">Edit</a>
                                 <a class="btn-sm btn-deactivate" href="delete_category.php?id=<?php echo $row['category_id']; ?>" onclick="return confirm('Deactivate this category?')">Deactivate</a>
                                 <a class="btn-sm btn-delete" href="delete_category_record.php?id=<?php echo $row['category_id']; ?>" onclick="return confirm('Move this category to deleted history?')">Delete</a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p class="empty-message">No categories found.</p>
        <?php endif; ?>
    </div>
<?php include 'footer.php'; ?>