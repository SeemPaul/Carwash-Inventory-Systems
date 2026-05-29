<?php
include 'auth.php';
include 'role_check.php';
require_role(['Admin', 'Manager', 'Owner']);
include 'config.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $service_name = trim($_POST['service_name']);
    $description = trim($_POST['description']);
    $estimated_duration = trim($_POST['estimated_duration']);
    $status = trim($_POST['status']);

    if (!empty($service_name)) {
        $stmt = $conn->prepare("
            INSERT INTO services (service_name, description, estimated_duration, status)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("ssss", $service_name, $description, $estimated_duration, $status);

        if ($stmt->execute()) {
            $message = "Service added successfully!";
        } else {
            $message = "Error adding service.";
        }

        $stmt->close();
    } else {
        $message = "Service name is required.";
    }
}

include 'header.php';
?>

<div class="main-wrapper">
    <?php include 'sidebar.php'; ?>

    <div class="content">
    <h1 class="page-title">Add Service</h1>
    
    <?php include 'breadcrumb.php'; ?>

    <div class="page-card">
        <a class="action-btn" href="services.php">View Services</a>
        <a class="action-btn" href="service_products.php">Assign Products</a>
        <a class="action-btn" href="dashboard.php">Dashboard</a>
    </div>

    <div class="page-card">
        <form method="POST">
            <label>Service Name</label>
            <input type="text" name="service_name" required style="width:100%; padding:10px; margin-top:6px; margin-bottom:15px;">

            <label>Description</label>
            <textarea name="description" style="width:100%; padding:10px; margin-top:6px; margin-bottom:15px; min-height:90px; resize:vertical;"></textarea>

            <label>Estimated Duration</label>
            <input type="text" name="estimated_duration" placeholder="e.g. 20 minutes" style="width:100%; padding:10px; margin-top:6px; margin-bottom:15px;">

            <label>Status</label>
            <select name="status" required style="width:100%; padding:10px; margin-top:6px; margin-bottom:15px;">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>

            <button type="submit" class="action-btn" style="border:none; cursor:pointer;">Save Service</button>
        </form>

        <?php if (!empty($message)): ?>
            <p style="margin-top:15px; font-weight:bold; color:green;">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>
    </div>

<?php include 'footer.php'; ?>