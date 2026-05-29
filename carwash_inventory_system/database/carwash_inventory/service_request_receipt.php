<?php
include 'auth.php';
include 'config.php';

if (!isset($_GET['id'])) {
    die("Request ID is missing.");
}

$request_id = (int)$_GET['id'];

$sql = "
    SELECT 
        sr.request_id,
        sr.request_code,
        sr.customer_name,
        sr.vehicle_type,
        sr.plate_number,
        sr.status,
        sr.created_at,
        s.service_name,
        s.description,
        s.estimated_duration
    FROM service_requests sr
    INNER JOIN services s ON sr.service_id = s.service_id
    WHERE sr.request_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $request_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    die("Service request not found.");
}

$request = $result->fetch_assoc();
$stmt->close();

// Included products
$product_sql = "
    SELECT 
        p.product_name,
        p.unit,
        sp.quantity_used
    FROM service_products sp
    INNER JOIN products p ON sp.product_id = p.product_id
    INNER JOIN service_requests sr ON sp.service_id = sr.service_id
    WHERE sr.request_id = ?
    ORDER BY p.product_name ASC
";

$product_stmt = $conn->prepare($product_sql);
$product_stmt->bind_param("i", $request_id);
$product_stmt->execute();
$products_result = $product_stmt->get_result();

include 'header.php';
?>

<div class="main-wrapper">
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <h1 class="page-title">Service Request Slip</h1>

        <div class="page-card">
            <a class="action-btn" href="service_request.php">New Request</a>
            <a class="action-btn" href="#" onclick="window.print(); return false;">Print / Save PDF</a>
        </div>

        <div class="page-card">
            <h2 style="margin-top:0;">Carwash Service Request</h2>
            <p><strong>Request Code:</strong> <?php echo htmlspecialchars($request['request_code']); ?></p>
            <p><strong>Date:</strong> <?php echo htmlspecialchars($request['created_at']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($request['status']); ?></p>

            <hr style="margin:20px 0;">

            <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($request['customer_name'] ?: 'N/A'); ?></p>
            <p><strong>Vehicle Type:</strong> <?php echo htmlspecialchars($request['vehicle_type'] ?: 'N/A'); ?></p>
            <p><strong>Plate Number:</strong> <?php echo htmlspecialchars($request['plate_number'] ?: 'N/A'); ?></p>

            <hr style="margin:20px 0;">

            <p><strong>Service Selected:</strong> <?php echo htmlspecialchars($request['service_name']); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($request['description'] ?: 'No description provided.'); ?></p>
            <p><strong>Estimated Duration:</strong> <?php echo htmlspecialchars($request['estimated_duration'] ?: 'Not specified.'); ?></p>

            <h3>Included Products</h3>

            <?php if ($products_result && $products_result->num_rows > 0): ?>
                <table>
                    <tr>
                        <th>Product Name</th>
                        <th>Unit</th>
                        <th>Quantity Used</th>
                    </tr>

                    <?php while ($row = $products_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['unit']); ?></td>
                            <td><?php echo (int)$row['quantity_used']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p class="empty-message">No included products found.</p>
            <?php endif; ?>

            <p style="margin-top:20px;"><strong>Note:</strong> Present this request slip to the cashier for further processing.</p>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>