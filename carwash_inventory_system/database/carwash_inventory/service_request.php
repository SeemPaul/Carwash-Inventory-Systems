<?php
include 'auth.php';
include 'config.php';

$message = "";
$error = "";

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = trim($_POST['customer_name'] ?? '');
    $vehicle_type = trim($_POST['vehicle_type'] ?? '');
    $plate_number = trim($_POST['plate_number'] ?? '');
    $service_id = (int)($_POST['service_id'] ?? 0);

    if ($service_id <= 0) {
        $error = "Please select a service.";
    } else {
        $request_code = 'SR-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid((string)mt_rand(), true)), 0, 6));

        $stmt = $conn->prepare("
            INSERT INTO service_requests (request_code, customer_name, vehicle_type, plate_number, service_id)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssi", $request_code, $customer_name, $vehicle_type, $plate_number, $service_id);

        if ($stmt->execute()) {
            $new_request_id = $stmt->insert_id;
            $stmt->close();

            header("Location: service_request_receipt.php?id=" . $new_request_id);
            exit();
        } else {
            $error = "Failed to create service request.";
        }

        $stmt->close();
    }
}

include 'header.php';
?>

<div class="main-wrapper">
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <h1 class="page-title">Service Request</h1>
            <?php include 'breadcrumb.php'; ?>

        <div class="page-card">
            <a class="action-btn" href="dashboard.php">Dashboard</a>
            <a class="action-btn" href="services.php">Services</a>
            <a class="action-btn" href="log_service.php">Log Service Usage</a>
        </div>

        <div class="page-card">
            <form method="POST">
                <label>Customer Name</label>
                <input type="text" name="customer_name" placeholder="Optional">

                <label>Vehicle Type</label>
                <input type="text" name="vehicle_type" placeholder="e.g. Sedan, Motorcycle, SUV">

                <label>Plate Number</label>
                <input type="text" name="plate_number" placeholder="Optional">

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

                <button type="submit" class="action-btn" style="border:none; cursor:pointer;">Generate Request Slip</button>
            </form>

            <?php if (!empty($message)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
        </div>


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

    <?php include 'footer.php'; ?>