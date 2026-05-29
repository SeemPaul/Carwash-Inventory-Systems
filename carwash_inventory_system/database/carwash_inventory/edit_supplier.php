<?php
include 'auth.php';
include 'role_check.php';
require_role(['Admin', 'Manager', 'Owner']);
include 'config.php';

if (!isset($_GET['id'])) {
    die("Supplier ID is missing.");
}

$id = (int)$_GET['id'];

$result = $conn->query("SELECT * FROM suppliers WHERE supplier_id = $id");

if (!$result || $result->num_rows == 0) {
    die("Supplier not found.");
}

$supplier = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $supplier_name = trim($_POST['supplier_name']);
    $contact_person = trim($_POST['contact_person']);
    $phone = trim($_POST['phone']);

    $stmt = $conn->prepare("
        UPDATE suppliers
        SET supplier_name = ?, contact_person = ?, phone = ?
        WHERE supplier_id = ?
    ");
    $stmt->bind_param("sssi", $supplier_name, $contact_person, $phone, $id);

    if ($stmt->execute()) {
        header("Location: suppliers.php");
        exit();
    } else {
        echo "Update failed.";
    }

    $stmt->close();
}

include 'header.php';
include 'sidebar.php';
?>

<div class="content">
    <h1 class="page-title">Edit Supplier</h1>

    <div class="page-card">
        <form method="POST">
            <label>Supplier Name</label>
            <input type="text" name="supplier_name" value="<?php echo htmlspecialchars($supplier['supplier_name']); ?>" required style="width:100%; padding:10px; margin-top:6px; margin-bottom:15px;">

            <label>Contact Person</label>
            <input type="text" name="contact_person" value="<?php echo htmlspecialchars($supplier['contact_person']); ?>" style="width:100%; padding:10px; margin-top:6px; margin-bottom:15px;">

            <label>Phone Number</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($supplier['phone']); ?>" style="width:100%; padding:10px; margin-top:6px; margin-bottom:15px;">

            <button type="submit" class="action-btn" style="border:none; cursor:pointer;">Update Supplier</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>