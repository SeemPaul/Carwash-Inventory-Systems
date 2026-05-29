<?php
include 'auth.php';
include 'role_check.php';
require_role(['Admin', 'Manager', 'Owner', 'Staff']);
include 'config.php';

$days_range = 30;

$sql = "
    SELECT 
        p.product_id,
        p.product_name,
        p.unit,
        p.quantity_in_stock,
        p.reorder_level,
        p.status,
        COALESCE(SUM(CASE WHEN il.log_type = 'OUT' THEN il.quantity ELSE 0 END), 0) AS recent_usage
    FROM products p
    LEFT JOIN inventory_logs il 
        ON p.product_id = il.product_id
        AND il.date_logged >= DATE_SUB(NOW(), INTERVAL ? DAY)
    WHERE p.quantity_in_stock <= p.reorder_level
    GROUP BY 
        p.product_id,
        p.product_name,
        p.unit,
        p.quantity_in_stock,
        p.reorder_level,
        p.status
    ORDER BY p.quantity_in_stock ASC
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
        <h1 class="page-title">Inventory Notifications</h1>

        <?php include 'breadcrumb.php'; ?>

        <div class="page-card">
            <a class="action-btn" href="dashboard.php">Dashboard</a>
            <a class="action-btn" href="inventory.php">Inventory</a>
            <a class="action-btn" href="reorder_list.php">Reorder List</a>
            <a class="action-btn" href="stock_in.php">Stock In / Restock</a>
            <a class="action-btn" href="inventory_forecast.php">Inventory Forecast</a>
        </div>

        <div class="page-card">
            <?php if ($result && $result->num_rows > 0): ?>
                <table>
                    <tr>
                        <th>Product</th>
                        <th>Unit</th>
                        <th>Current Stock</th>
                        <th>Reorder Level</th>
                        <th>Recent Usage</th>
                        <th>Forecast Status</th>
                        <th>Estimated Days Left</th>
                        <th>Action</th>
                    </tr>

                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php
                        $stock = (int)$row['quantity_in_stock'];
                        $reorder = (int)$row['reorder_level'];
                        $recent_usage = (int)$row['recent_usage'];
                        $avg_daily_usage = $recent_usage > 0 ? $recent_usage / $days_range : 0;

                        if ($avg_daily_usage > 0) {
                            $days_left = floor($stock / $avg_daily_usage);
                        } else {
                            $days_left = null;
                        }

                        if ($stock == 0) {
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
                            $forecast_status = "LOW STOCK";
                            $forecast_class = "low-stock-text";
                            $days_display = $days_left . " day(s)";
                        }
                        ?>

                        <tr>
                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['unit']); ?></td>
                            <td><?php echo $stock; ?></td>
                            <td><?php echo $reorder; ?></td>
                            <td><?php echo $recent_usage; ?> used in last <?php echo $days_range; ?> days</td>
                            <td class="<?php echo $forecast_class; ?>">
                                <?php echo $forecast_status; ?>
                            </td>
                            <td><?php echo htmlspecialchars($days_display); ?></td>
                            <td>
                                <?php if (in_array($_SESSION['role'], ['Admin', 'Manager', 'Owner'])): ?>
    <a class="action-btn" href="stock_in.php">Restock</a>
<?php else: ?>
    <div class="empty-state">
    <h3>Notify Admin</h3>  
</div>
<?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <div class="alert alert-success">
                    No inventory alerts. All products are above reorder level.
                </div>
            <?php endif; ?>
        </div>

<?php include 'footer.php'; ?>