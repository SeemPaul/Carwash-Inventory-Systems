<?php
include 'auth.php';
include 'role_check.php';
require_role(['Admin', 'Manager', 'Owner', 'Staff']);
include 'config.php';

$days_range = $_GET['days_range'] ?? 30;
$days_range = (int)$days_range;

if ($days_range <= 0) {
    $days_range = 30;
}

$sql = "
    SELECT 
        p.product_id,
        p.product_name,
        p.unit,
        p.quantity_in_stock,
        p.reorder_level,
        COALESCE(SUM(CASE WHEN il.log_type = 'OUT' THEN il.quantity ELSE 0 END), 0) AS total_used
    FROM products p
    LEFT JOIN inventory_logs il 
        ON p.product_id = il.product_id
        AND il.date_logged >= DATE_SUB(NOW(), INTERVAL ? DAY)
    WHERE p.status = 'Active'
    GROUP BY 
        p.product_id,
        p.product_name,
        p.unit,
        p.quantity_in_stock,
        p.reorder_level
    ORDER BY p.product_name ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $days_range);
$stmt->execute();
$result = $stmt->get_result();

include 'header.php';
?>

<div class="main-wrapper">
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <h1 class="page-title">Inventory Forecasting</h1>

        <?php include 'breadcrumb.php'; ?>

        <div class="page-card">
            <a class="action-btn" href="dashboard.php">Dashboard</a>
            <a class="action-btn" href="inventory.php">Inventory</a>
            <a class="action-btn" href="reorder_list.php">Reorder List</a>
            <a class="action-btn" href="notifications.php">Notifications</a>
        </div>

        <div class="page-card">
            <h3 class="filter-title">Forecast Settings</h3>

            <form method="GET" class="filter-form">
                <div class="filter-group">
                    <label>Usage Range</label>
                    <select name="days_range">
                        <option value="7" <?php if ($days_range == 7) echo 'selected'; ?>>Last 7 Days</option>
                        <option value="15" <?php if ($days_range == 15) echo 'selected'; ?>>Last 15 Days</option>
                        <option value="30" <?php if ($days_range == 30) echo 'selected'; ?>>Last 30 Days</option>
                        <option value="60" <?php if ($days_range == 60) echo 'selected'; ?>>Last 60 Days</option>
                        <option value="90" <?php if ($days_range == 90) echo 'selected'; ?>>Last 90 Days</option>
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="action-btn">Apply Forecast</button>
                    <a href="inventory_forecast.php" class="action-btn" style="background:#6b7280;">Reset</a>
                </div>
            </form>
        </div>

        <div class="page-card">
            <h3>Forecast Results</h3>

            <?php if ($result && $result->num_rows > 0): ?>
                <table>
                    <tr>
                        <th>Product</th>
                        <th>Unit</th>
                        <th>Current Stock</th>
                        <th>Total Used</th>
                        <th>Average Daily Usage</th>
                        <th>Estimated Days Left</th>
                        <th>Forecast Status</th>
                    </tr>

                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php
                        $current_stock = (int)$row['quantity_in_stock'];
                        $total_used = (int)$row['total_used'];
                        $avg_daily_usage = $total_used > 0 ? $total_used / $days_range : 0;

                        if ($avg_daily_usage > 0) {
                            $days_left = floor($current_stock / $avg_daily_usage);
                        } else {
                            $days_left = null;
                        }

                        if ($current_stock == 0) {
                            $forecast_status = "OUT OF STOCK";
                            $forecast_class = "low-stock-text";
                            $days_display = "0";
                        } elseif ($avg_daily_usage == 0) {
                            $forecast_status = "NO RECENT USAGE";
                            $forecast_class = "warning-text";
                            $days_display = "N/A";
                        } elseif ($days_left <= 3) {
                            $forecast_status = "HIGH RISK";
                            $forecast_class = "low-stock-text";
                            $days_display = $days_left . " day(s)";
                        } elseif ($days_left <= 7) {
                            $forecast_status = "RESTOCK SOON";
                            $forecast_class = "warning-text";
                            $days_display = $days_left . " day(s)";
                        } else {
                            $forecast_status = "STABLE";
                            $forecast_class = "success-text";
                            $days_display = $days_left . " day(s)";
                        }
                        ?>

                        <tr>
                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['unit']); ?></td>
                            <td><?php echo $current_stock; ?></td>
                            <td><?php echo $total_used; ?></td>
                            <td><?php echo number_format($avg_daily_usage, 2); ?></td>
                            <td><?php echo htmlspecialchars($days_display); ?></td>
                            <td class="<?php echo $forecast_class; ?>">
                                <?php echo $forecast_status; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <div class="empty-state">
    <i class="fa-solid fa-box-open"></i>
    <h3>No records found</h3>
    <p>No active products found for forecasting.</p>
</div>
            <?php endif; ?>
        </div>
<?php include 'footer.php'; ?>