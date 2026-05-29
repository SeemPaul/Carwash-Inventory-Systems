<?php
include 'auth.php';
include 'role_check.php';
require_role(['Admin', 'Manager', 'Owner']);
include 'config.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $supplier_name = trim($_POST['supplier_name']);
    $contact_person = trim($_POST['contact_person']);
    $phone = trim($_POST['phone']);

    if (!empty($supplier_name)) {
        $stmt = $conn->prepare("
            INSERT INTO suppliers (supplier_name, contact_person, phone)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("sss", $supplier_name, $contact_person, $phone);

        if ($stmt->execute()) {
            $message = "Supplier added successfully!";
        } else {
            $message = "Error adding supplier.";
        }

        $stmt->close();
    } else {
        $message = "Supplier name is required.";
    }
}

include 'header.php';
?>

<div class="main-wrapper">
    <?php include 'sidebar.php'; ?>

    <div class="content">
    <h1 class="page-title">Add Supplier</h1>

    <?php include 'breadcrumb.php'; ?>
   

    <div class="page-card">
        <a class="action-btn" href="suppliers.php">View Suppliers</a>
        <a class="action-btn" href="add_product.php">Add Product</a>
        <a class="action-btn" href="inventory.php">View Inventory</a>
        <a class="action-btn" href="dashboard.php">Dashboard</a>
    </div>

    <div class="page-card">
        <form method="POST">
            <label>Supplier Name</label>
            <input type="text" name="supplier_name" required style="width:100%; padding:10px; margin-top:6px; margin-bottom:15px;">

            <label>Contact Person</label>
            <input type="text" name="contact_person" style="width:100%; padding:10px; margin-top:6px; margin-bottom:15px;">

            <label>Phone Number</label>
            <input type="text" name="phone" style="width:100%; padding:10px; margin-top:6px; margin-bottom:15px;">

            <button type="submit" class="action-btn" style="border:none; cursor:pointer;">Save Supplier</button>
        </form>

        <?php if (!empty($message)): ?>
            <p style="margin-top:15px; font-weight:bold; color:green;">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>
    </div>

<?php include 'footer.php'; ?>