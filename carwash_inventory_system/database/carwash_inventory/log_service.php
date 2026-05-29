<?php
include 'auth.php';
include 'config.php';

$message = "";
$error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $service_id = (int)$_POST['service_id'];
    $remarks = trim($_POST['remarks']);
    $user_id = $_SESSION['user_id'];
    $date_performed = date("Y-m-d H:i:s");

    if ($service_id > 0) {
        $conn->begin_transaction();

        try {
            // Get all products linked to the selected service
            $sql = "
                SELECT 
                    sp.product_id,
                    sp.quantity_used,
                    p.product_name,
                    p.quantity_in_stock
                FROM service_products sp
                INNER JOIN products p ON sp.product_id = p.product_id
                WHERE sp.service_id = ?
            ";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $service_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 0) {
                throw new Exception("No products assigned to this service yet.");
            }

            $products_to_deduct = [];
            $insufficient_products = [];

            while ($row = $result->fetch_assoc()) {
                $product_id = (int)$row['product_id'];
                $quantity_used = (int)$row['quantity_used'];
                $product_name = $row['product_name'];
                $quantity_in_stock = (int)$row['quantity_in_stock'];

                if ($quantity_in_stock < $quantity_used) {
                    $insufficient_products[] = $product_name . " (Available: " . $quantity_in_stock . ", Needed: " . $quantity_used . ")";
                } else {
                    $products_to_deduct[] = [
                        'product_id' => $product_id,
                        'quantity_used' => $quantity_used
                    ];
                }
            }

            $stmt->close();

            if (!empty($insufficient_products)) {
                throw new Exception("Insufficient stock for: " . implode(", ", $insufficient_products));
            }

            // Insert service log
            $stmt = $conn->prepare("
                INSERT INTO service_logs (user_id, service_id, date_performed, remarks)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param("iiss", $user_id, $service_id, $date_performed, $remarks);

            if (!$stmt->execute()) {
                throw new Exception("Failed to save service log.");
            }

            $stmt->close();

            // Deduct inventory
            foreach ($products_to_deduct as $product) {
$update = $conn->prepare("
    UPDATE products
    SET quantity_in_stock = quantity_in_stock - ?
    WHERE product_id = ?
");
$update->bind_param("ii", $product['quantity_used'], $product['product_id']);

if (!$update->execute()) {
    throw new Exception("Failed to deduct inventory.");
}

// Insert into inventory logs (Stock OUT)
$log_stmt = $conn->prepare("
    INSERT INTO inventory_logs (product_id, log_type, quantity, remarks)
    VALUES (?, 'OUT', ?, ?)
");

$remarks_log = "Used for service ID: " . $service_id;
$log_stmt->bind_param("iis", $product['product_id'], $product['quantity_used'], $remarks_log);

if (!$log_stmt->execute()) {
    throw new Exception("Failed to log inventory OUT transaction.");
}

$log_stmt->close();
$update->close();
            }

            $conn->commit();
            $message = "Service logged successfully and inventory updated!";
        } catch (Exception $e) {
            $conn->rollback();
            $error = $e->getMessage();
        }
    } else {
        $error = "Please select a service.";
    }
}

// Fetch active services
$services = $conn->query("
    SELECT service_id, service_name, description, estimated_duration
    FROM services
    WHERE status = 'Active'
    ORDER BY service_name ASC
");

// Build service details array
$service_details = [];

$service_details_sql = "
    SELECT 
        s.service_id,
        s.service_name,
        s.description,
        s.estimated_duration,
        p.product_name,
        p.unit,
        sp.quantity_used
    FROM services s
    LEFT JOIN service_products sp ON s.service_id = sp.service_id
    LEFT JOIN products p ON sp.product_id = p.product_id
    WHERE s.status = 'Active'
    ORDER BY s.service_name ASC, p.product_name ASC
";

$details_result = $conn->query($service_details_sql);

while ($row = $details_result->fetch_assoc()) {
    $service_id = (int)$row['service_id'];

    if (!isset($service_details[$service_id])) {
        $service_details[$service_id] = [
            'service_name' => $row['service_name'],
            'description' => $row['description'],
            'estimated_duration' => $row['estimated_duration'],
            'products' => []
        ];
    }

    if (!empty($row['product_name'])) {
        $service_details[$service_id]['products'][] = [
            'product_name' => $row['product_name'],
            'unit' => $row['unit'],
            'quantity_used' => $row['quantity_used']
        ];
    }
}

include 'header.php';
?>

<div class="main-wrapper">
    <?php include 'sidebar.php'; ?>

    <div class="content">
    <h1 class="page-title">Log Service Usage</h1>
        <?php include 'breadcrumb.php'; ?>

    <div class="page-card">
        <a class="action-btn" href="usage_logs.php">View Usage Logs</a>
        <a class="action-btn" href="inventory.php">View Inventory</a>
        <a class="action-btn" href="services.php">View Services</a>
        <a class="action-btn" href="dashboard.php">Dashboard</a>
    </div>

    <div class="page-card">
        <form method="POST">
            <label>Select Service</label>
            <select name="service_id" id="service_id" required onchange="showServiceDetails()">
                <option value="">Select Service</option>
                <?php while ($row = $services->fetch_assoc()): ?>
                    <option value="<?php echo $row['service_id']; ?>">
                        <?php echo htmlspecialchars($row['service_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <div id="service-details-box" style="margin-top:20px; padding:15px; border:1px solid #e5e7eb; border-radius:10px; background:#f8fafc; display:none;">
                <h3 style="margin-top:0;">Service Details</h3>
                <p><strong>Description:</strong> <span id="detail-description">—</span></p>
                <p><strong>Estimated Duration:</strong> <span id="detail-duration">—</span></p>

                <h4>Included Products</h4>
                <div id="detail-products">—</div>
            </div>

            <label>Remarks</label>
            <textarea name="remarks" placeholder="Optional notes..." style="width:100%; padding:10px; margin-top:6px; margin-bottom:15px; min-height:90px; resize:vertical;"></textarea>

            <button type="submit" class="action-btn" style="border:none; cursor:pointer;">Save Usage Log</button>
        </form>

<?php if (!empty($message)): ?>
    <div class="alert alert-success">
        <p style="margin-top:15px; font-weight:bold; color:green;">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-error">
        <p style="margin-top:15px; font-weight:bold; color:red;">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>
    </div>
<?php include 'footer.php'; ?>

<script>
const serviceDetails = <?php echo json_encode($service_details, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

function showServiceDetails() {
    const serviceId = document.getElementById('service_id').value;
    const box = document.getElementById('service-details-box');
    const description = document.getElementById('detail-description');
    const duration = document.getElementById('detail-duration');
    const products = document.getElementById('detail-products');

    if (!serviceId || !serviceDetails[serviceId]) {
        box.style.display = 'none';
        description.textContent = '—';
        duration.textContent = '—';
        products.innerHTML = '—';
        return;
    }

    const details = serviceDetails[serviceId];

    description.textContent = details.description ? details.description : 'No description provided.';
    duration.textContent = details.estimated_duration ? details.estimated_duration : 'Not specified.';

    if (details.products.length > 0) {
        let html = '<ul style="margin:0; padding-left:20px;">';
        details.products.forEach(product => {
            html += `<li>${product.product_name} - ${product.quantity_used} (${product.unit})</li>`;
        });
        html += '</ul>';
        products.innerHTML = html;
    } else {
        products.innerHTML = 'No products assigned yet.';
    }

    box.style.display = 'block';
}
</script>

