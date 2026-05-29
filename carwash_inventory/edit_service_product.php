<?php
include 'auth.php';
include 'role_check.php';
require_role(['Admin', 'Manager', 'Owner']);
include 'config.php';

if (!isset($_GET['id'])) {
    die("Mapping ID is missing.");
}

$id = (int)$_GET['id'];

$sql = "
    SELECT 
        sp.*,
        s.service_name,
        p.product_name,
        p.unit
    FROM service_products sp
    INNER JOIN services s ON sp.service_id = s.service_id
    INNER JOIN products p ON sp.product_id = p.product_id
    WHERE sp.service_product_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows == 0) {
    die("Mapping not found.");
}

$mapping = $result->fetch_assoc();
$stmt->close();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $quantity_used = (int)$_POST['quantity_used'];

    if ($quantity_used > 0) {
        $stmt = $conn->prepare("
            UPDATE service_products
            SET quantity_used = ?
            WHERE service_product_id = ?
        ");
        $stmt->bind_param("ii", $quantity_used, $id);

        if ($stmt->execute()) {
            header("Location: service_products.php");
            exit();
        } else {
            $message = "Error updating mapping.";
        }

        $stmt->close();
    } else {
        $message = "Quantity used must be greater than 0.";
    }
}

include 'header.php';
include 'sidebar.php';
?>

<div class="content">
    <h1 class="page-title">Edit Service-Product Mapping</h1>

    <div class="page-card">
        <a class="action-btn" href="service_products.php">Back to Mappings</a>
        <a class="action-btn" href="services.php">View Services</a>
        <a class="action-btn" href="inventory.php">View Inventory</a>
    </div>

    <div class="page-card">
        <form method="POST">
            <label>Service</label>
            <input type="text" value="<?php echo htmlspecialchars($mapping['service_name']); ?>" readonly>

            <label>Product</label>
            <input type="text" value="<?php echo htmlspecialchars($mapping['product_name'] . ' (' . $mapping['unit'] . ')'); ?>" readonly>

            <label>Quantity Used</label>
            <input type="number" name="quantity_used" min="1" value="<?php echo (int)$mapping['quantity_used']; ?>" required>

            <button type="submit" class="action-btn" style="border:none; cursor:pointer;">Update Mapping</button>
        </form>

        <?php if (!empty($message)): ?>
            <p style="margin-top:15px; font-weight:bold; color:red;">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>