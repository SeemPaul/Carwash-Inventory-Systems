<?php
$current_page = basename($_SERVER['PHP_SELF']);

$page_titles = [
    'dashboard.php' => 'Dashboard',
    'inventory.php' => 'Inventory',
    'stock_in.php' => 'Stock In / Restock',
    'categories.php' => 'Categories',
    'add_product.php' => 'Add Product',
    'services.php' => 'Services',
    'service_request.php' => 'Service Request',
    'service_request_receipt.php' => 'Service Request Slip',
    'service_products.php' => 'Assign Products',
    'log_service.php' => 'Log Service Usage',
    'usage_logs.php' => 'Usage Logs',
    'reports.php' => 'Reports',
    'inventory_logs.php' => 'Inventory History',
    'suppliers.php' => 'Suppliers'
];

$current_title = $page_titles[$current_page] ?? 'Current Page';
$previous_page = $_SERVER['HTTP_REFERER'] ?? 'dashboard.php';
?>

<div class="breadcrumb-card">
    <div class="breadcrumb-path">
        <a href="dashboard.php">Dashboard</a>
        <span>›</span>
        <span><?php echo htmlspecialchars($current_title); ?></span>
    </div>

    <a class="back-link" href="<?php echo htmlspecialchars($previous_page); ?>">← Back to Previous Page</a>
</div>