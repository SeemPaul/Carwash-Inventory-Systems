<?php
include 'auth.php';
include 'role_check.php';
require_role(['Admin', 'Manager', 'Owner']);
include 'config.php';

// Date filter inputs
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

// Build date condition
$date_condition = "";
$date_params = [];
$date_types = "";

if (!empty($date_from) && !empty($date_to)) {
    $date_condition = " WHERE DATE(sl.date_performed) BETWEEN ? AND ? ";
    $date_types = "ss";
    $date_params[] = $date_from;
    $date_params[] = $date_to;
} elseif (!empty($date_from)) {
    $date_condition = " WHERE DATE(sl.date_performed) >= ? ";
    $date_types = "s";
    $date_params[] = $date_from;
} elseif (!empty($date_to)) {
    $date_condition = " WHERE DATE(sl.date_performed) <= ? ";
    $date_types = "s";
    $date_params[] = $date_to;
}

// Global summary counts
$total_products_result = $conn->query("SELECT COUNT(*) AS total_products FROM products");
$total_products = $total_products_result->fetch_assoc()['total_products'];

$low_stock_result = $conn->query("SELECT COUNT(*) AS low_stock FROM products WHERE quantity_in_stock <= reorder_level");
$low_stock = $low_stock_result->fetch_assoc()['low_stock'];

$active_services_result = $conn->query("SELECT COUNT(*) AS active_services FROM services WHERE status = 'Active'");
$active_services = $active_services_result->fetch_assoc()['active_services'];

// Total logs with optional date filter
$total_logs_sql = "SELECT COUNT(*) AS total_logs FROM service_logs sl" . $date_condition;
$total_logs_stmt = $conn->prepare($total_logs_sql);
if (!empty($date_types)) {
    $total_logs_stmt->bind_param($date_types, ...$date_params);
}
$total_logs_stmt->execute();
$total_logs_result = $total_logs_stmt->get_result();
$total_logs = $total_logs_result->fetch_assoc()['total_logs'];
$total_logs_stmt->close();

// Low stock products (current inventory state)
$low_stock_products_sql = "
    SELECT product_name, quantity_in_stock, reorder_level
    FROM products
    WHERE quantity_in_stock <= reorder_level
    ORDER BY quantity_in_stock ASC
";
$low_stock_products = $conn->query($low_stock_products_sql);

// Recent usage logs with optional date filter
$recent_logs_sql = "
    SELECT 
        sl.service_log_id,
        s.service_name,
        u.full_name,
        sl.date_performed,
        sl.remarks
    FROM service_logs sl
    INNER JOIN services s ON sl.service_id = s.service_id
    INNER JOIN users u ON sl.user_id = u.user_id
    " . $date_condition . "
    ORDER BY sl.date_performed DESC
    LIMIT 10
";
$recent_logs_stmt = $conn->prepare($recent_logs_sql);
if (!empty($date_types)) {
    $recent_logs_stmt->bind_param($date_types, ...$date_params);
}
$recent_logs_stmt->execute();
$recent_logs = $recent_logs_stmt->get_result();
$recent_logs_stmt->close();

// Most used services with optional date filter
$most_used_services_sql = "
    SELECT 
        s.service_name,
        COUNT(sl.service_log_id) AS usage_count
    FROM service_logs sl
    INNER JOIN services s ON sl.service_id = s.service_id
    " . $date_condition . "
    GROUP BY s.service_id, s.service_name
    ORDER BY usage_count DESC
    LIMIT 10
";
$most_used_services_stmt = $conn->prepare($most_used_services_sql);
if (!empty($date_types)) {
    $most_used_services_stmt->bind_param($date_types, ...$date_params);
}
$most_used_services_stmt->execute();
$most_used_services = $most_used_services_stmt->get_result();
$most_used_services_stmt->close();

// Most used products with optional date filter
$most_used_products_sql = "
    SELECT 
        p.product_name,
        p.unit,
        SUM(sp.quantity_used) AS total_quantity_used
    FROM service_logs sl
    INNER JOIN service_products sp ON sl.service_id = sp.service_id
    INNER JOIN products p ON sp.product_id = p.product_id
    " . $date_condition . "
    GROUP BY p.product_id, p.product_name, p.unit
    ORDER BY total_quantity_used DESC
    LIMIT 10
";
$most_used_products_stmt = $conn->prepare($most_used_products_sql);
if (!empty($date_types)) {
    $most_used_products_stmt->bind_param($date_types, ...$date_params);
}
$most_used_products_stmt->execute();
$most_used_products = $most_used_products_stmt->get_result();
$most_used_products_stmt->close();

include 'header.php';
?>

<div class="main-wrapper">
    <?php include 'sidebar.php'; ?>

    <div class="content">
    <h1 class="page-title">Reports</h1>
        <?php include 'breadcrumb.php'; ?>

    <div class="page-card">
        <a class="action-btn" href="dashboard.php">Dashboard</a>
        <a class="action-btn" href="inventory.php">View Inventory</a>
        <a class="action-btn" href="usage_logs.php">Usage Logs</a>
        <a class="action-btn" href="services.php">Services</a>
        <a class="action-btn" href="#" onclick="window.print(); return false;">Export PDF</a>
    </div>

<div class="page-card">
    <h3 class="filter-title">Filter Reports by Date</h3>

    <form method="GET" class="filter-form">
        <div class="filter-group">
            <label>From Date</label>
            <input type="date" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>">
        </div>

        <div class="filter-group">
            <label>To Date</label>
            <input type="date" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>">
        </div>

        <div class="filter-actions">
            <button type="submit" class="action-btn">Apply Filter</button>
            <a class="action-btn" href="reports.php" style="background:#6b7280;">Clear</a>
        </div>
    </form>
</div>

    <div class="cards">
        <div class="card blue">
            <h2><?php echo $total_products; ?></h2>
            <p>Total Products</p>
        </div>

        <div class="card red">
            <h2><?php echo $low_stock; ?></h2>
            <p>Low Stock Items</p>
        </div>

        <div class="card green">
            <h2><?php echo $total_logs; ?></h2>
            <p>Total Services Logged<?php echo (!empty($date_from) || !empty($date_to)) ? ' (Filtered)' : ''; ?></p>
        </div>

        <div class="card orange">
            <h2><?php echo $active_services; ?></h2>
            <p>Active Services</p>
        </div>
    </div>

    <div class="page-card">
        <h3>Low Stock Products</h3>

        <?php if ($low_stock_products && $low_stock_products->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity in Stock</th>
                    <th>Reorder Level</th>
                    <th>Status</th>
                </tr>
                <?php while ($row = $low_stock_products->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td><?php echo (int)$row['quantity_in_stock']; ?></td>
                        <td><?php echo (int)$row['reorder_level']; ?></td>
                        <td class="low-stock-text">LOW STOCK</td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p class="empty-message">No low stock products found.</p>
        <?php endif; ?>
    </div>

    <div class="page-card">
        <h3>Recent Usage Logs</h3>

        <?php if ($recent_logs && $recent_logs->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Log ID</th>
                    <th>Service</th>
                    <th>Logged By</th>
                    <th>Date Performed</th>
                    <th>Remarks</th>
                </tr>
                <?php while ($row = $recent_logs->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo (int)$row['service_log_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['date_performed']); ?></td>
                        <td><?php echo htmlspecialchars($row['remarks']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p class="empty-message">No usage logs found for the selected date range.</p>
        <?php endif; ?>
    </div>

    <div class="page-card">
        <h3>Most Used Services</h3>

        <?php if ($most_used_services && $most_used_services->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Service Name</th>
                    <th>Times Logged</th>
                </tr>
                <?php while ($row = $most_used_services->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                        <td><?php echo (int)$row['usage_count']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p class="empty-message">No service usage data found for the selected date range.</p>
        <?php endif; ?>
    </div>

    <div class="page-card">
        <h3>Most Used Products</h3>

        <?php if ($most_used_products && $most_used_products->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Product Name</th>
                    <th>Unit</th>
                    <th>Total Quantity Used</th>
                </tr>
                <?php while ($row = $most_used_products->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['unit']); ?></td>
                        <td><?php echo (int)$row['total_quantity_used']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p class="empty-message">No product usage data found for the selected date range.</p>
        <?php endif; ?>
    </div>

<?php include 'footer.php'; ?>