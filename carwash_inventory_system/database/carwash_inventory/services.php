<?php
include 'auth.php';
include 'config.php';


$sql = "SELECT * FROM services 
        WHERE deleted_status = 'Active' 
        ORDER BY service_name ASC";$result = $conn->query($sql);

include 'header.php';
?>

<div class="main-wrapper">
    <?php include 'sidebar.php'; ?>

    <div class="content">
    <h1 class="page-title">Services List</h1>
        <?php include 'breadcrumb.php'; ?>

    <div class="page-card">
<?php if (in_array($_SESSION['role'], ['Admin', 'Manager', 'Owner'])): ?>
    <a class="action-btn" href="add_service.php">Add Service</a>
    <a class="action-btn" href="service_products.php">Assign Products</a>
<?php endif; ?>
        <a class="action-btn" href="dashboard.php">Dashboard</a>
    </div>

    <div class="page-card">
        <table>
            <tr>
                <th>Service Name</th>
                <th>Description</th>
                <th>Estimated Duration</th>
                <th>Status</th>
                <?php if (in_array($_SESSION['role'], ['Admin', 'Manager', 'Owner'])): ?>
    <th>Actions</th>
<?php endif; ?>
            </tr>

            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo htmlspecialchars($row['estimated_duration']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
<?php if (in_array($_SESSION['role'], ['Admin', 'Manager', 'Owner'])): ?>
    <td>
<div class="action-group">
    <a class="btn-sm btn-edit" href="edit_service.php?id=<?php echo $row['service_id']; ?>">Edit</a>
    <a class="btn-sm btn-deactivate" href="delete_service.php?id=<?php echo $row['service_id']; ?>" onclick="return confirm('Deactivate this service?')">Deactivate</a>
    <a class="btn-sm btn-delete" href="delete_service_record.php?id=<?php echo $row['service_id']; ?>" onclick="return confirm('Move this service to deleted history?')">Delete</a>
</div>
    </td>
<?php endif; ?>
                </tr>
            <?php endwhile; ?>
        </table>
      </div>

<?php include 'footer.php'; ?>