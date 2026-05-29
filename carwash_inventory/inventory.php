<?php
include 'auth.php';
include 'config.php';

$search = $_GET['search'] ?? '';
$category_filter = $_GET['category_id'] ?? '';
$supplier_filter = $_GET['supplier_id'] ?? '';
$status_filter = $_GET['status'] ?? '';
$stock_filter = $_GET['stock_filter'] ?? '';

$conditions = [];
$params = [];
$types = "";

$conditions[] = "p.deleted_status = 'Active'";

if (!empty($search)) {
    $conditions[] = "p.product_name LIKE ?";
    $params[] = "%" . $search . "%";
    $types .= "s";
}

if (!empty($category_filter)) {
    $conditions[] = "p.category_id = ?";
    $params[] = $category_filter;
    $types .= "i";
}

if (!empty($supplier_filter)) {
    $conditions[] = "p.supplier_id = ?";
    $params[] = $supplier_filter;
    $types .= "i";
}

if (!empty($status_filter)) {
    $conditions[] = "p.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

if ($stock_filter == "low") {
    $conditions[] = "p.quantity_in_stock <= p.reorder_level";
}

$where = "";
if (!empty($conditions)) {
    $where = "WHERE " . implode(" AND ", $conditions);
}

$sql = "
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
    $where
    ORDER BY p.product_name ASC
";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$categories = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_name ASC");
$suppliers = $conn->query("SELECT supplier_id, supplier_name FROM suppliers ORDER BY supplier_name ASC");

include 'header.php';
?>

<div class="main-wrapper">
    <?php include 'sidebar.php'; ?>

    <div class="content">
    <h1 class="page-title">Inventory List</h1>
    <?php include 'breadcrumb.php'; ?>

<div class="page-card">
    <h3 class="filter-title">Filter Inventory</h3>

    <form method="GET" class="filter-form">
        <div class="filter-group">
            <label>Search Product</label>
            <input type="text" name="search" placeholder="Enter product name..." value="<?php echo htmlspecialchars($search); ?>">
        </div>

        <div class="filter-group">
            <label>Category</label>
            <select name="category_id">
                <option value="">All Categories</option>
                <?php while ($cat = $categories->fetch_assoc()): ?>
                    <option value="<?php echo $cat['category_id']; ?>" <?php if ($category_filter == $cat['category_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($cat['category_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="filter-group">
            <label>Supplier</label>
            <select name="supplier_id">
                <option value="">All Suppliers</option>
                <?php while ($sup = $suppliers->fetch_assoc()): ?>
                    <option value="<?php echo $sup['supplier_id']; ?>" <?php if ($supplier_filter == $sup['supplier_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($sup['supplier_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="filter-group">
            <label>Status</label>
            <select name="status">
                <option value="">All Status</option>
                <option value="Active" <?php if ($status_filter == 'Active') echo 'selected'; ?>>Active</option>
                <option value="Inactive" <?php if ($status_filter == 'Inactive') echo 'selected'; ?>>Inactive</option>
            </select>
        </div>

        <div class="filter-group">
            <label>Stock Alert</label>
            <select name="stock_filter">
                <option value="">All Stock</option>
                <option value="low" <?php if ($stock_filter == 'low') echo 'selected'; ?>>Low Stock Only</option>
            </select>
        </div>

        <div class="filter-actions">
            <button type="submit" class="action-btn">Apply Filter</button>
            <a href="inventory.php" class="action-btn" style="background:#6b7280;">Clear</a>
        </div>
    </form>
</div>

    <div class="page-card">
<?php if (in_array($_SESSION['role'], ['Admin', 'Manager', 'Owner'])): ?>
    <a class="action-btn" href="add_product.php">Add Product</a>
    <a class="action-btn" href="add_category.php">Add Category</a>
    <a class="action-btn" href="add_supplier.php">Add Supplier</a>
<?php endif; ?>
    </div>

    <div class="page-card">
        <table>
            <tr>
                <th>Product Name</th>
                <th>Category</th>
                <th>Supplier</th>
                <th>Unit</th>
                <th>Stock</th>
                <th>Reorder Level</th>
                <th>Status</th>
                <th>Stock Alert</th>
                <?php if (in_array($_SESSION['role'], ['Admin', 'Manager', 'Owner'])): ?>
    <th>Actions</th>
<?php endif; ?>
            </tr>

            <?php while ($row = $result->fetch_assoc()): ?>
                <?php $isLow = $row['quantity_in_stock'] <= $row['reorder_level']; ?>
                <?php
$stock = (int)$row['quantity_in_stock'];
$reorder = (int)$row['reorder_level'];

if ($stock <= 0) {
    $stock_status = "OUT OF STOCK";
    $stock_class = "low-stock-text";
} elseif ($stock <= 2) {
    $stock_status = "CRITICAL";
    $stock_class = "warning-text";
} elseif ($stock <= $reorder) {
    $stock_status = "LOW STOCK";
    $stock_class = "low-stock-text";
} else {
    $stock_status = "OK";
    $stock_class = "success-text";
}
?>
                <tr>
                    <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['category_name'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($row['supplier_name'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($row['unit']); ?></td>
                    <td><?php echo (int)$row['quantity_in_stock']; ?></td>
                    <td><?php echo (int)$row['reorder_level']; ?></td>
                    <td>
    <?php if ($row['status'] == 'Active'): ?>
        <span class="status-badge badge-active">Active</span>
    <?php elseif ($row['status'] == 'Inactive'): ?>
        <span class="status-badge badge-inactive">Inactive</span>
    <?php else: ?>
        <span class="status-badge badge-inactive">
            <?php echo htmlspecialchars($row['status']); ?>
        </span>
    <?php endif; ?>
</td>
                    <td>
    <?php if ($stock_status == 'OUT OF STOCK'): ?>
        <span class="status-badge badge-out">Out of Stock</span>
    <?php elseif ($stock_status == 'CRITICAL'): ?>
        <span class="status-badge badge-critical">Critical</span>
    <?php elseif ($stock_status == 'LOW STOCK'): ?>
        <span class="status-badge badge-low">Low Stock</span>
    <?php else: ?>
        <span class="status-badge badge-active">OK</span>
    <?php endif; ?>
</td>
                
                    <?php
$stock = (int)$row['quantity_in_stock'];
$reorder = (int)$row['reorder_level'];

?>
<?php if (in_array($_SESSION['role'], ['Admin', 'Manager', 'Owner'])): ?>
    <td>
<div class="action-group">
    <a class="btn-sm btn-view" href="product_history.php?id=<?php echo $row['product_id']; ?>">History</a>
    <a class="btn-sm btn-edit" href="edit_product.php?id=<?php echo $row['product_id']; ?>">Edit</a>
    <a class="btn-sm btn-deactivate" href="delete_product.php?id=<?php echo $row['product_id']; ?>" onclick="return confirm('Deactivate this product?')">Deactivate</a>
    <a class="btn-sm btn-delete" href="delete_product_record.php?id=<?php echo $row['product_id']; ?>" onclick="return confirm('Move this product to deleted history?')">Delete</a>
</div>
    </td>
<?php endif; ?>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

<?php include 'footer.php'; ?>