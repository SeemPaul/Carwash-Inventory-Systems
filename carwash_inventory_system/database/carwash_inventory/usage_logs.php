<?php
include 'auth.php';
include 'config.php';

$sql = "
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
";

$result = $conn->query($sql);

include 'header.php';
?>

<div class="main-wrapper">
    <?php include 'sidebar.php'; ?>

    <div class="content">
    <h1 class="page-title">Usage Logs</h1>
        <?php include 'breadcrumb.php'; ?>

    <div class="page-card">
        <a class="action-btn" href="log_service.php">Log Service Usage</a>
        <a class="action-btn" href="inventory.php">View Inventory</a>
        <a class="action-btn" href="dashboard.php">Dashboard</a>
    </div>

    <div class="page-card">
        <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Log ID</th>
                    <th>Service Name</th>
                    <th>Logged By</th>
                    <th>Date Performed</th>
                    <th>Remarks</th>
                </tr>

                <?php while ($row = $result->fetch_assoc()): ?>
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
            <p class="empty-message">No usage logs found yet.</p>
        <?php endif; ?>
    </div>

<?php include 'footer.php'; ?>