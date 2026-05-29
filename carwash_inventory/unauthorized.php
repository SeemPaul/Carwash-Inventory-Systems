<?php
include 'auth.php';
include 'config.php';
include 'header.php';
?>

<div class="main-wrapper">
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <h1 class="page-title">Access Denied</h1>

        <div class="page-card">
            <div class="alert alert-error">
                You do not have permission to access this page.
            </div>

            <a class="action-btn" href="dashboard.php">Back to Dashboard</a>
        </div>

<?php include 'footer.php'; ?>