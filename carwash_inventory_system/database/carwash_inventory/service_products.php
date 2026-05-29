<?php
include 'auth.php';
include 'role_check.php';
require_role(['Admin', 'Manager', 'Owner']);
include 'config.php';

$message = "";
$message_type = "success";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $service_id = (int)($_POST['service_id'] ?? 0);
    $selected_products = $_POST['selected_products'] ?? [];
    $quantities = $_POST['quantity_used'] ?? [];

    if ($service_id <= 0) {
        $message = "Please select a service.";
        $message_type = "error";
    } elseif (empty($selected_products)) {
        $message = "Please select at least one product to assign.";
        $message_type = "error";
    } else {
        // Get existing product mappings for this service
        $existing_products = [];
        $existing_stmt = $conn->prepare("
            SELECT product_id
            FROM service_products
            WHERE service_id = ?
        ");
        $existing_stmt->bind_param("i", $service_id);
        $existing_stmt->execute();
        $existing_result = $existing_stmt->get_result();

        while ($row = $existing_result->fetch_assoc()) {
            $existing_products[] = (int)$row['product_id'];
        }
        $existing_stmt->close();

        // Get product names for better error messages
        $product_name_map = [];
        $product_name_result = $conn->query("
            SELECT product_id, product_name, unit
            FROM products
            WHERE status = 'Active'
            ORDER BY product_name ASC
        ");
        while ($row = $product_name_result->fetch_assoc()) {
            $product_name_map[(int)$row['product_id']] = $row['product_name'] . " (" . $row['unit'] . ")";
        }

        $duplicates = [];
        $invalid_quantities = [];
        $valid_rows = [];

        foreach ($selected_products as $product_id_raw) {
            $product_id = (int)$product_id_raw;
            $quantity_used = (int)($quantities[$product_id] ?? 0);

            if (in_array($product_id, $existing_products, true)) {
                $duplicates[] = $product_name_map[$product_id] ?? ("Product ID " . $product_id);
                continue;
            }

            if ($quantity_used <= 0) {
                $invalid_quantities[] = $product_name_map[$product_id] ?? ("Product ID " . $product_id);
                continue;
            }

            $valid_rows[] = [
                'product_id' => $product_id,
                'quantity_used' => $quantity_used
            ];
        }

        if (!empty($duplicates)) {
            $message = "These products are already assigned to the selected service: " . implode(", ", $duplicates);
            $message_type = "error";
        } elseif (!empty($invalid_quantities)) {
            $message = "Please enter a valid quantity for: " . implode(", ", $invalid_quantities);
            $message_type = "error";
        } elseif (empty($valid_rows)) {
            $message = "No valid product assignments were submitted.";
            $message_type = "error";
        } else {
            $conn->begin_transaction();

            try {
                $insert_stmt = $conn->prepare("
                    INSERT INTO service_products (service_id, product_id, quantity_used)
                    VALUES (?, ?, ?)
                ");

                foreach ($valid_rows as $row) {
                    $insert_stmt->bind_param("iii", $service_id, $row['product_id'], $row['quantity_used']);
                    if (!$insert_stmt->execute()) {
                        throw new Exception("Failed to assign one or more products.");
                    }
                }

                $insert_stmt->close();
                $conn->commit();

                $message = "Products assigned successfully!";
                $message_type = "success";
            } catch (Exception $e) {
                $conn->rollback();
                $message = "Error assigning products.";
                $message_type = "error";
            }
        }
    }
}

// Fetch services
$services = $conn->query("
    SELECT service_id, service_name
    FROM services
    WHERE status = 'Active'
    ORDER BY service_name ASC
");

// Fetch products
$products = $conn->query("
    SELECT product_id, product_name, unit
    FROM products
    WHERE status = 'Active'
    ORDER BY product_name ASC
");

// Fetch assigned mappings
$assigned_sql = "
    SELECT 
        sp.service_product_id,
        s.service_name,
        p.product_name,
        p.unit,
        sp.quantity_used
    FROM service_products sp
    INNER JOIN services s ON sp.service_id = s.service_id
    INNER JOIN products p ON sp.product_id = p.product_id
    ORDER BY s.service_name ASC, p.product_name ASC
";
$assigned_result = $conn->query($assigned_sql);

include 'header.php';
?>

<div class="main-wrapper">
    <?php include 'sidebar.php'; ?>

    <div class="content">
    <h1 class="page-title">Assign Products to Services</h1>
        <?php include 'breadcrumb.php'; ?>

    <div class="page-card">
        <a class="action-btn" href="services.php">View Services</a>
        <a class="action-btn" href="add_service.php">Add Service</a>
        <a class="action-btn" href="inventory.php">View Inventory</a>
        <a class="action-btn" href="dashboard.php">Dashboard</a>
    </div>

    <div class="page-card">
        <form method="POST">
            <label>Select Service</label>
            <select name="service_id" required>
                <option value="">Select Service</option>
                <?php while ($row = $services->fetch_assoc()): ?>
                    <option value="<?php echo $row['service_id']; ?>">
                        <?php echo htmlspecialchars($row['service_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <h3 style="margin-top:20px;">Applicable Products</h3>
            <p class="empty-message" style="margin-bottom:15px;">
                Check the products that may be used for this service, then enter the quantity used for each selected product.
            </p>

            <table>
                <tr>
                    <th style="width: 90px;">Use</th>
                    <th>Product</th>
                    <th style="width: 160px;">Quantity Used</th>
                </tr>

                <?php while ($row = $products->fetch_assoc()): ?>
                    <?php $product_id = (int)$row['product_id']; ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="selected_products[]" value="<?php echo $product_id; ?>" onchange="toggleQuantity(<?php echo $product_id; ?>, this)">
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row['product_name'] . " (" . $row['unit'] . ")"); ?>
                        </td>
                        <td>
                            <input 
                                type="number" 
                                name="quantity_used[<?php echo $product_id; ?>]" 
                                id="qty_<?php echo $product_id; ?>" 
                                min="1" 
                                placeholder="Qty" 
                                disabled
                            >
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>

            <br>
            <button type="submit" class="action-btn" style="border:none; cursor:pointer;">Assign Products</button>
        </form>

        <?php if (!empty($message)): ?>
            <p class="<?php echo $message_type === 'success' ? 'success-text' : 'low-stock-text'; ?>" style="margin-top:15px;">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>
    </div>

    <div class="page-card">
        <h3>Assigned Products per Service</h3>

        <?php if ($assigned_result && $assigned_result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Service</th>
                    <th>Product</th>
                    <th>Unit</th>
                    <th>Quantity Used</th>
                    <th>Actions</th>
                </tr>

                <?php while ($row = $assigned_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['unit']); ?></td>
                        <td><?php echo (int)$row['quantity_used']; ?></td>
                        <td>
                            <a href="edit_service_product.php?id=<?php echo $row['service_product_id']; ?>">Edit</a> |
                            <a href="delete_service_product.php?id=<?php echo $row['service_product_id']; ?>" onclick="return confirm('Delete this mapping?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p class="empty-message">No product-to-service assignments found yet.</p>
        <?php endif; ?>
    </div>
<?php include 'footer.php'; ?>

<script>
function toggleQuantity(productId, checkbox) {
    const qtyInput = document.getElementById('qty_' + productId);
    qtyInput.disabled = !checkbox.checked;

    if (!checkbox.checked) {
        qtyInput.value = '';
    } else {
        qtyInput.focus();
    }
}
</script>

