<?php
include 'auth.php';
include 'role_check.php';
require_role(['Admin', 'Manager', 'Owner']);
include 'config.php';
include 'audit_helper.php';

$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = trim($_POST['product_name']);
    $category_id = (int)$_POST['category_id'];
    $supplier_id = (int)$_POST['supplier_id'];
    $unit = trim($_POST['unit']);
    $quantity = (int)$_POST['quantity'];
    $reorder_level = (int)$_POST['reorder_level'];
    $status = trim($_POST['status']);

    if (!empty($product_name) && $category_id > 0 && $supplier_id > 0 && !empty($unit)) {
        $stmt = $conn->prepare("
            INSERT INTO products 
            (product_name, category_id, supplier_id, unit, quantity_in_stock, reorder_level, status)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param("siisiis", $product_name, $category_id, $supplier_id, $unit, $quantity, $reorder_level, $status);

        if ($stmt->execute()) {
            log_audit(
    $conn,
    'Added Product',
    'Products',
    'Added new product: ' . $product_name
);
            $message = "Product added successfully!";
        } else {
            $message = "Error adding product.";
        }

        $stmt->close();
    } else {
        $message = "Please complete all required fields.";
    }
}

// Fetch categories
$categories = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");

// Fetch suppliers
$suppliers = $conn->query("SELECT * FROM suppliers ORDER BY supplier_name ASC");

include 'header.php';
?>

<div class="main-wrapper">
    <?php include 'sidebar.php'; ?>

    <div class="content">
    <h1 class="page-title">Add Product</h1>
        <?php include 'breadcrumb.php'; ?>

    <div class="page-card">
        <a class="action-btn" href="inventory.php">View Inventory</a>
        <a class="action-btn" href="add_category.php">Add Category</a>
        <a class="action-btn" href="add_supplier.php">Add Supplier</a>
        <a class="action-btn" href="dashboard.php">Dashboard</a>
    </div>

    <div class="page-card">
        <form method="POST">
            <label>Product Name</label>
            <input type="text" name="product_name" required style="width:100%; padding:10px; margin-top:6px; margin-bottom:15px;">

            <label>Category</label>
            <select name="category_id" required style="width:100%; padding:10px; margin-top:6px; margin-bottom:15px;">
                <option value="">Select Category</option>
                <?php while ($row = $categories->fetch_assoc()): ?>
                    <option value="<?php echo $row['category_id']; ?>">
                        <?php echo htmlspecialchars($row['category_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Supplier</label>
            <select name="supplier_id" required style="width:100%; padding:10px; margin-top:6px; margin-bottom:15px;">
                <option value="">Select Supplier</option>
                <?php while ($row = $suppliers->fetch_assoc()): ?>
                    <option value="<?php echo $row['supplier_id']; ?>">
                        <?php echo htmlspecialchars($row['supplier_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Unit</label>
            <input type="text" name="unit" placeholder="e.g. pcs, ml, liters" required style="width:100%; padding:10px; margin-top:6px; margin-bottom:15px;">

            <label>Quantity in Stock</label>
            <input type="number" name="quantity" required style="width:100%; padding:10px; margin-top:6px; margin-bottom:15px;">

            <label>Reorder Level</label>
            <input type="number" name="reorder_level" required style="width:100%; padding:10px; margin-top:6px; margin-bottom:15px;">

            <label>Status</label>
            <select name="status" required style="width:100%; padding:10px; margin-top:6px; margin-bottom:15px;">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>

            <button type="submit" class="action-btn" style="border:none; cursor:pointer;">Save Product</button>
        </form>

        <?php if (!empty($message)): ?>
            <p style="margin-top:15px; font-weight:bold; color:green;">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>
    </div>

<?php include 'footer.php'; ?>