<?php
include 'auth.php';
include 'role_check.php';
require_role(['Admin', 'Manager', 'Owner']);
include 'config.php';

$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$type = $_GET['type'] ?? '';

// Build conditions
$conditions = [];
$params = [];
$types = "";

if (!empty($date_from)) {
    $conditions[] = "DATE(il.date_logged) >= ?";
    $params[] = $date_from;
    $types .= "s";
}

if (!empty($date_to)) {
    $conditions[] = "DATE(il.date_logged) <= ?";
    $params[] = $date_to;
    $types .= "s";
}

if (!empty($type)) {
    $conditions[] = "il.log_type = ?";
    $params[] = $type;
    $types .= "s";
}

$where = "";
if (!empty($conditions)) {
    $where = "WHERE " . implode(" AND ", $conditions);
}

$sql = "
    SELECT 
        il.log_id,
        p.product_name,
        p.unit,
        il.log_type,
        il.quantity,
        il.remarks,
        il.date_logged
    FROM inventory_logs il
    INNER JOIN products p ON il.product_id = p.product_id
    $where
    ORDER BY il.date_logged DESC
";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

include 'header.php';
?>

<div class="main-wrapper">
    <?php include 'sidebar.php'; ?>

    <div class="content">
    <h1 class="page-title">Inventory Transaction History</h1>
        <?php include 'breadcrumb.php'; ?>

    <div class="page-card">
        <a class="action-btn" href="inventory.php">Inventory</a>
        <a class="action-btn" href="stock_in.php">Stock In</a>
        <a class="action-btn" href="dashboard.php">Dashboard</a>
        <a class="action-btn" href="#" onclick="window.print(); return false;">Export PDF</a>
    </div>

    <div class="page-card">
        <?php if ($result && $result->num_rows > 0): ?>
               <h3 class="filter-title">Filter Inventory Logs</h3>

        <form method="GET" class="filter-form">
        <div class="filter-group">
        <label>From Date</label>
        <input type="date" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>">
   </div>

        <div class="filter-group">
        <label>To Date</label>
        <input type="date" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>">
   </div>

         <div class="filter-group">
        <label>Type</label>
        <select name="type">
            <option value="">All</option>
            <option value="IN" <?php if ($type == 'IN') echo 'selected'; ?>>Stock In</option>
            <option value="OUT" <?php if ($type == 'OUT') echo 'selected'; ?>>Stock Out</option>
        </select>
        </div>

         <div class="filter-actions">
        <button type="submit" class="action-btn">Apply Filter</button>
        <a href="inventory_logs.php" class="action-btn" style="background:#6b7280;">Clear</a>
         </div>
    </form>
</div>

<!-- EXISTING TABLE -->
<div class="page-card">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>Remarks</th>
                    <th>Date</th>
                </tr>

                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo (int)$row['log_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['product_name'] . " (" . $row['unit'] . ")"); ?></td>
                        <td class="<?php echo $row['log_type'] === 'IN' ? 'success-text' : 'low-stock-text'; ?>">
                            <?php echo htmlspecialchars($row['log_type']); ?>
                        </td>
                        <td><?php echo (int)$row['quantity']; ?></td>
                        <td><?php echo htmlspecialchars($row['remarks']); ?></td>
                        <td><?php echo htmlspecialchars($row['date_logged']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p class="empty-message">No inventory history found yet.</p>
        <?php endif; ?>
    </div>

<?php include 'footer.php'; ?>