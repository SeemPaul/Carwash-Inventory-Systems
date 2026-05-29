<?php
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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Usage Logs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fb;
            padding: 30px;
        }
        h2 {
            margin-bottom: 20px;
        }
        .top-links {
            margin-bottom: 20px;
        }
        .top-links a {
            margin-right: 15px;
            text-decoration: none;
            color: #007bff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.08);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background: #007bff;
            color: white;
        }
        .empty-message {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.08);
        }
    </style>
</head>
<body>

    <h2>Usage Logs</h2>

    <div class="top-links">
        <a href="log_service.php">Log Service Usage</a>
        <a href="inventory.php">View Inventory</a>
        <a href="dashboard.php">Dashboard</a>
    </div>

    <?php if ($result->num_rows > 0): ?>
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
        <div class="empty-message">
            No usage logs found yet.
        </div>
    <?php endif; ?>

</body>
</html>