<?php
include 'auth.php';
include 'role_check.php';
require_role(['Admin', 'Manager', 'Owner']);
include 'config.php';

$sql = "
    SELECT 
        al.audit_id,
        u.full_name,
        u.role,
        al.action,
        al.module,
        al.description,
        al.created_at
    FROM audit_logs al
    LEFT JOIN users u ON al.user_id = u.user_id
    ORDER BY al.created_at DESC
";

$result = $conn->query($sql);

include 'header.php';
?>

<div class="main-wrapper">
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <h1 class="page-title">Audit Logs</h1>

        <?php include 'breadcrumb.php'; ?>

        <div class="page-card">
            <a class="action-btn" href="dashboard.php">Dashboard</a>
            <a class="action-btn" href="inventory_logs.php">Inventory History</a>
            <a class="action-btn" href="reports.php">Reports</a>
        </div>

        <div class="page-card">
            <?php if ($result && $result->num_rows > 0): ?>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Action</th>
                        <th>Module</th>
                        <th>Description</th>
                        <th>Date</th>
                    </tr>

                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo (int)$row['audit_id']; ?></td>
                            <td><?php echo htmlspecialchars($row['full_name'] ?? 'Unknown User'); ?></td>
                            <td><?php echo htmlspecialchars($row['role'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['action']); ?></td>
                            <td><?php echo htmlspecialchars($row['module']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p class="empty-message">No audit logs found yet.</p>
            <?php endif; ?>
        </div>

<?php include 'footer.php'; ?>