<?php
include 'auth.php';
include 'config.php';

// Summary counts
$total_products_result = $conn->query("SELECT COUNT(*) AS total_products FROM products");
$total_products = $total_products_result->fetch_assoc()['total_products'];

$low_stock_result = $conn->query("SELECT COUNT(*) AS low_stock FROM products WHERE quantity_in_stock <= reorder_level");
$low_stock = $low_stock_result->fetch_assoc()['low_stock'];

$total_logs_result = $conn->query("SELECT COUNT(*) AS total_logs FROM service_logs");
$total_logs = $total_logs_result->fetch_assoc()['total_logs'];

$active_services_result = $conn->query("SELECT COUNT(*) AS active_services FROM services WHERE status = 'Active'");
$active_services = $active_services_result->fetch_assoc()['active_services'];

// Recent usage logs
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
    ORDER BY sl.date_performed DESC
    LIMIT 5
";
$recent_logs = $conn->query($recent_logs_sql);

// Low stock products
$low_stock_products_sql = "
    SELECT product_name, quantity_in_stock, reorder_level
    FROM products
    WHERE quantity_in_stock <= reorder_level
    ORDER BY quantity_in_stock ASC
    LIMIT 5
";
$low_stock_products = $conn->query($low_stock_products_sql);

// Most used services
$most_used_services_sql = "
    SELECT 
        s.service_name,
        COUNT(sl.service_log_id) AS usage_count
    FROM service_logs sl
    INNER JOIN services s ON sl.service_id = s.service_id
    GROUP BY s.service_id, s.service_name
    ORDER BY usage_count DESC
    LIMIT 5
";
$most_used_services = $conn->query($most_used_services_sql);

// Most used products
$most_used_products_sql = "
    SELECT 
        p.product_name,
        p.unit,
        SUM(sp.quantity_used) AS total_quantity_used
    FROM service_logs sl
    INNER JOIN service_products sp ON sl.service_id = sp.service_id
    INNER JOIN products p ON sp.product_id = p.product_id
    GROUP BY p.product_id, p.product_name, p.unit
    ORDER BY total_quantity_used DESC
    LIMIT 5
";
$most_used_products = $conn->query($most_used_products_sql);

// Convert results into arrays for chart display
$max_service_usage = 1;
$max_product_usage = 1;

$services_data = [];
if ($most_used_services && $most_used_services->num_rows > 0) {
    while ($row = $most_used_services->fetch_assoc()) {
        $services_data[] = $row;
        if ((int)$row['usage_count'] > $max_service_usage) {
            $max_service_usage = (int)$row['usage_count'];
        }
    }
}

$products_data = [];
if ($most_used_products && $most_used_products->num_rows > 0) {
    while ($row = $most_used_products->fetch_assoc()) {
        $products_data[] = $row;
        if ((int)$row['total_quantity_used'] > $max_product_usage) {
            $max_product_usage = (int)$row['total_quantity_used'];
        }
    }
}

// Fast Moving Products
$fast_moving_sql = "
    SELECT 
        p.product_name,
        p.unit,
        SUM(il.quantity) AS total_used
    FROM inventory_logs il
    INNER JOIN products p ON il.product_id = p.product_id
    WHERE il.log_type = 'OUT'
    GROUP BY p.product_id, p.product_name, p.unit
    ORDER BY total_used DESC
    LIMIT 5
";
$fast_moving_products = $conn->query($fast_moving_sql);

// Slow Moving Products
$slow_moving_sql = "
    SELECT 
        p.product_name,
        p.unit,
        COALESCE(SUM(CASE WHEN il.log_type = 'OUT' THEN il.quantity ELSE 0 END), 0) AS total_used
    FROM products p
    LEFT JOIN inventory_logs il ON p.product_id = il.product_id
    WHERE p.status = 'Active'
    GROUP BY p.product_id, p.product_name, p.unit
    ORDER BY total_used ASC
    LIMIT 5
";
$slow_moving_products = $conn->query($slow_moving_sql);

// Out of Stock
$out_stock_result = $conn->query("
    SELECT COUNT(*) AS total
    FROM products
    WHERE status = 'Active'
    AND deleted_status = 'Active'
    AND quantity_in_stock <= 0
");
$out_of_stock = $out_stock_result->fetch_assoc()['total'];

// Critical Stock: stock is 1 or 2
$critical_stock_result = $conn->query("
    SELECT COUNT(*) AS total
    FROM products
    WHERE status = 'Active'
    AND deleted_status = 'Active'
    AND quantity_in_stock > 0
    AND quantity_in_stock <= 2
");
$critical_stock = $critical_stock_result->fetch_assoc()['total'];

// Low Stock: above critical but still at/below reorder level
$low_stock_result = $conn->query("
    SELECT COUNT(*) AS total
    FROM products
    WHERE status = 'Active'
    AND deleted_status = 'Active'
    AND quantity_in_stock > 2
    AND quantity_in_stock <= reorder_level
");
$low_stock_count = $low_stock_result->fetch_assoc()['total'];
// Urgent stock alerts
$urgent_stock_sql = "
    SELECT 
        product_name,
        unit,
        quantity_in_stock,
        reorder_level
    FROM products
    WHERE status = 'Active'
    AND quantity_in_stock <= reorder_level
    ORDER BY quantity_in_stock ASC
    LIMIT 5
";
$urgent_stock = $conn->query($urgent_stock_sql);// Urgent stock alerts
$urgent_stock_sql = "
    SELECT 
        product_name,
        unit,
        quantity_in_stock,
        reorder_level
    FROM products
    WHERE status = 'Active'
    AND quantity_in_stock <= reorder_level
    ORDER BY quantity_in_stock ASC
    LIMIT 5
";
$urgent_stock = $conn->query($urgent_stock_sql);
include 'header.php';
?>

<div class="main-wrapper">
    <?php include 'sidebar.php'; ?>

    <div class="content">

<div class="content">

<div class="dashboard-hero">
    <h1>
        Welcome back,
        <?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username']); ?>
    </h1>

    <p>
        Monitor stock levels, inventory alerts, usage activity,
        forecasting insights, and inventory performance in one place.
    </p>

    <div class="hero-role">
        <i class="fa-solid fa-user-shield"></i>
        <?php echo htmlspecialchars($_SESSION['role']); ?>
    </div>
</div>
   
<div class="system-status-grid">
    <div class="status-box">
        <i class="fa-solid fa-clock"></i> 
        <div>
            <strong>Date and Time Today</strong>
                    <strong id="live-date"> <?php echo date('F d, Y'); ?> </strong>
                <span id="live-time"> <?php echo date('h:i:s A'); ?> </span>           
        </div>
    </div>

    <div class="status-box">
        <i class="fa-solid fa-user-shield"></i>
        <div>
            <strong>Currently Logged In As</strong>
            <span><?php echo htmlspecialchars($_SESSION['role']); ?></span>
        </div>
    </div>

    <div class="status-box">
        <i class="fa-solid fa-bell"></i>
        <div>
            <strong>Inventory Alerts</strong>
            <span><?php echo $notification_count ?? 0; ?> active alert(s)</span>
        </div>
    </div>
</div>

    <div class="page-card">
        <div class="quick-actions-grid">
            <?php if (in_array($_SESSION['role'], ['Admin', 'Manager', 'Owner'])): ?>
            <a class="action-btn" href="add_product.php">         
    <i class="fa-solid fa-plus"></i> Add Product
</a>
            <?php endif; ?>
            <a class="action-btn" href="inventory.php">
    <i class="fa-solid fa-boxes-stacked"></i> Inventory
</a>
            <a class="action-btn" href="services.php">
    <i class="fa-solid fa-spray-can-sparkles"></i> Services
</a>
            <?php if (in_array($_SESSION['role'], ['Admin', 'Manager', 'Owner'])): ?>
            <a class="action-btn" href="add_service.php">
    <i class="fa-solid fa-circle-plus"></i> Add Service
</a>
            <a class="action-btn" href="service_products.php">
    <i class="fa-solid fa-link"></i> Assign Products
</a>
            <?php endif; ?>
            <a class="action-btn" href="log_service.php">
    <i class="fa-solid fa-clipboard-check"></i> Log Usage
</a>
            <a class="action-btn" href="usage_logs.php">
    <i class="fa-solid fa-file-lines"></i> Usage Logs
</a>
            <?php if (in_array($_SESSION['role'], ['Admin', 'Manager', 'Owner'])): ?>
            <a class="action-btn" href="reports.php">
    <i class="fa-solid fa-chart-pie"></i> Reports
</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="cards">
        <a href="inventory.php" class="card-link">
           <div class="card blue">
    <i class="fa-solid fa-boxes-stacked card-icon"></i>
<h2 class="counter" data-target="<?php echo $total_products; ?>">0</h2>
    <p>Total Products</p>
</div>
        </a>

        <a href="reports.php" class="card-link">
            <div class="card red">
                <i class="fa-solid fa-triangle-exclamation card-icon"></i> <!-- Low Stock -->
                <h2 class="counter" data-target="<?php echo $low_stock; ?>">0</h2>
                <p>Low Stock Items</p>
            </div>
        </a>

        <a href="usage_logs.php" class="card-link">
            <div class="card green">
                <i class="fa-solid fa-clipboard-check card-icon"></i> <!-- Total Services Logged -->
                <h2 class="counter" data-target="<?php echo $total_logs; ?>">0</h2>
                <p>Total Services Logged</p>
            </div>
        </a>

        <a href="services.php" class="card-link">
            <div class="card orange">
                <i class="fa-solid fa-spray-can-sparkles card-icon"></i> <!-- Active Services -->
                <h2 class="counter" data-target="<?php echo $active_services; ?>">0</h2>
                <p>Active Services</p>
            </div>
        </a>
    </div>

    <div class="chart-stock-layout">
<div class="page-card chart-card dashboard-chart">
    <h3>Inventory Stock Status Chart</h3>
    <canvas id="stockStatusChart"></canvas>
</div>

    <div class="side-stock-alerts">
        <div class="card red">
            <i class="fa-solid fa-circle-xmark card-icon"></i>
            <h2 class="counter" data-target="<?php echo $out_of_stock; ?>">0</h2>
            <p>Out of Stock</p>
        </div>

        <div class="card orange">
            <i class="fa-solid fa-triangle-exclamation card-icon"></i>
            <h2 class="counter" data-target="<?php echo $critical_stock; ?>">0</h2>
            <p>Critical Stock</p>
        </div>

        <div class="card blue">
            <i class="fa-solid fa-arrow-trend-down card-icon"></i>
            <h2 class="counter" data-target="<?php echo $low_stock_count; ?>">0</h2>
            <p>Low Stock</p>
        </div>
    </div>

</div>

<div class="page-card">
    <h3>Urgent Stock Alerts</h3>

    <?php if ($urgent_stock && $urgent_stock->num_rows > 0): ?>
        <table class="compact-table">
            <tr>
                <th>Product</th>
                <th>Current Stock</th>
                <th>Reorder Level</th>
                <th>Status</th>
            </tr>

            <?php while ($row = $urgent_stock->fetch_assoc()): ?>
                <?php
                $stock = (int)$row['quantity_in_stock'];
                $reorder = (int)$row['reorder_level'];

                if ($stock == 0) {
                    $alert_status = "OUT OF STOCK";
                    $alert_class = "low-stock-text";
                } elseif ($stock <= 2) {
                    $alert_status = "CRITICAL";
                    $alert_class = "warning-text";
                } else {
                    $alert_status = "LOW STOCK";
                    $alert_class = "low-stock-text";
                }
                ?>

                <tr>
                    <td><?php echo htmlspecialchars($row['product_name'] . " (" . $row['unit'] . ")"); ?></td>
                    <td><?php echo $stock; ?></td>
                    <td><?php echo $reorder; ?></td>
                    <td class="<?php echo $alert_class; ?>"><?php echo $alert_status; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>

        <br>
        <a class="action-btn" href="notifications.php">View All Notifications</a>


        <a class="action-btn" href="reorder_list.php">Open Reorder List</a>

        
    <?php else: ?>
        <div class="alert alert-success">
            No urgent stock alerts. Inventory levels are currently stable.
        </div>
    <?php endif; ?>
</div>

<div class="dashboard-grid">
    <div class="page-card">
        <h3>Fast Moving Products</h3>

        <?php if ($fast_moving_products && $fast_moving_products->num_rows > 0): ?>
            <table class="compact-table">
                <tr>
                    <th>Product</th>
                    <th>Unit</th>
                    <th>Total Used</th>
                </tr>

                <?php while ($row = $fast_moving_products->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['unit']); ?></td>
                        <td><?php echo (int)$row['total_used']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p class="empty-message">No usage data available yet.</p>
        <?php endif; ?>
    </div>

    <div class="page-card">
        <h3>Slow Moving Products</h3>

        <?php if ($slow_moving_products && $slow_moving_products->num_rows > 0): ?>
            <table class="compact-table">
                <tr>
                    <th>Product</th>
                    <th>Unit</th>
                    <th>Total Used</th>
                </tr>

                <?php while ($row = $slow_moving_products->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['unit']); ?></td>
                        <td><?php echo (int)$row['total_used']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p class="empty-message">No product data available.</p>
        <?php endif; ?>
    </div>
</div>

    <div class="dashboard-grid">
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
                <p class="empty-message">No usage logs available yet.</p>
            <?php endif; ?>
        </div>

<div class="page-card">
    <h3>Most Used Services</h3>

    <?php if (!empty($services_data)): ?>
        <table class="compact-table">
            <tr>
                <th>Service Name</th>
                <th>Times Logged</th>
            </tr>

            <?php foreach ($services_data as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                    <td><?php echo (int)$row['usage_count']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p class="empty-message">No service usage data available yet.</p>
    <?php endif; ?>
</div>

<div class="page-card">
    <h3>Most Used Products</h3>

    <?php if (!empty($products_data)): ?>
        <table class="compact-table">
            <tr>
                <th>Product Name</th>
                <th>Unit</th>
                <th>Total Quantity Used</th>
            </tr>

            <?php foreach ($products_data as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['unit']); ?></td>
                    <td><?php echo (int)$row['total_quantity_used']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p class="empty-message">No product usage data available yet.</p>
    <?php endif; ?>
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
            <p class="empty-message">No low stock items right now.</p>
        <?php endif; ?>
    </div>
    
  <?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const stockStatusCtx = document.getElementById('stockStatusChart');

if (stockStatusCtx) {
    new Chart(stockStatusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Out of Stock', 'Critical Stock', 'Low Stock'],
            datasets: [{
                data: [
                    <?php echo $out_of_stock ?? 0; ?>,
                    <?php echo $critical_stock ?? 0; ?>,
                    <?php echo $low_stock_count ?? 0; ?>
                ],
                backgroundColor: [
                    '#dc2626',
                    '#f59e0b',
                    '#2563eb'
                ],
                borderWidth: 0
            }]
        },
        options: {
            plugins: {
                legend: {
                    labels: {
                        color: '#e5e7eb'
                    }
                }
            }
        }
    })
}
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const counters = document.querySelectorAll(".counter");

    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute("data-target")) || 0;
        let current = 0;

        const duration = 900;
        const stepTime = 20;
        const steps = duration / stepTime;
        const increment = target / steps;

        const updateCounter = setInterval(() => {
            current += increment;

            if (current >= target) {
                counter.innerText = target;
                clearInterval(updateCounter);
            } else {
                counter.innerText = Math.ceil(current);
            }
        }, stepTime);
    });
});
</script>

<script>
function updateClock() {

    const now = new Date();

    const dateOptions = {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };

    document.getElementById('live-date').innerText =
        now.toLocaleDateString(undefined, dateOptions);

    document.getElementById('live-time').innerText =
        now.toLocaleTimeString();
}

setInterval(updateClock, 1000);

updateClock();
</script>