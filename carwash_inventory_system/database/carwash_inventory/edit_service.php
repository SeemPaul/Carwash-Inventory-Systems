<?php
include 'auth.php';
include 'role_check.php';
require_role(['Admin', 'Manager', 'Owner']);
include 'config.php';
include 'audit_helper.php';

$message = "";

if (!isset($_GET['id'])) {
    die("Service ID is missing.");
}

$service_id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT * FROM services WHERE service_id = ?");
$stmt->bind_param("i", $service_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    die("Service not found.");
}

$service = $result->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $service_name = trim($_POST['service_name']);
    $description = trim($_POST['description']);
    $estimated_duration = trim($_POST['estimated_duration']);
    $status = trim($_POST['status']);

    $stmt = $conn->prepare("
        UPDATE services
        SET service_name = ?, description = ?, estimated_duration = ?, status = ?
        WHERE service_id = ?
    ");
    $stmt->bind_param("ssssi", $service_name, $description, $estimated_duration, $status, $service_id);

    if ($stmt->execute()) {

    log_audit(
        $conn,
        'Edited Service',
        'Services',
        'Updated service: ' . $service_name
    );

    header("Location: services.php");
    exit();
} else {
        $message = "Failed to update service.";
    }

    $stmt->close();
}

include 'header.php';
?>

<div class="main-wrapper">
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <h1 class="page-title">Edit Service</h1>

        <?php include 'breadcrumb.php'; ?>

        <div class="page-card">
            <a class="action-btn" href="services.php">Back to Services</a>
            <a class="action-btn" href="dashboard.php">Dashboard</a>
        </div>

        <div class="page-card">
            <form method="POST">
                <label>Service Name</label>
                <input type="text" name="service_name" value="<?php echo htmlspecialchars($service['service_name']); ?>" required>

                <label>Description</label>
                <textarea name="description" required><?php echo htmlspecialchars($service['description']); ?></textarea>

                <label>Estimated Duration</label>
                <input type="text" name="estimated_duration" value="<?php echo htmlspecialchars($service['estimated_duration']); ?>" required>

                <label>Status</label>
                <select name="status" required>
                    <option value="Active" <?php if ($service['status'] == 'Active') echo 'selected'; ?>>Active</option>
                    <option value="Inactive" <?php if ($service['status'] == 'Inactive') echo 'selected'; ?>>Inactive</option>
                </select>

                <button type="submit" class="action-btn">Update Service</button>
            </form>

            <?php if (!empty($message)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
        </div>

<?php include 'footer.php'; ?>