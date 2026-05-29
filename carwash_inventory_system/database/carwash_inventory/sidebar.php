<?php
$current_page = basename($_SERVER['PHP_SELF']);
$user_role = $_SESSION['role'] ?? '';
$is_admin = in_array($user_role, ['Admin', 'Manager', 'Owner']);

function is_active($pages, $current_page) {
    return in_array($current_page, (array)$pages) ? 'active' : '';
}

function is_group_open($pages, $current_page) {
    return in_array($current_page, (array)$pages) ? 'open' : '';
}

function is_toggle_active($pages, $current_page) {
    return in_array($current_page, (array)$pages) ? 'active' : '';
}
?>

<style>

.menu-section{
    font-size: 11px;
    font-weight: 700;
    color: #7f8aa3;
    letter-spacing: 1px;
    margin: 22px 18px 10px;
    text-transform: uppercase;
}

.sidebar a{
    display: block;
    padding: 12px 16px;
    border-radius: 10px;
    margin: 4px 10px;
    transition: 0.2s ease;
}

.sidebar a:hover{
    background: rgba(37, 99, 235, 0.18);
    transform: translateX(3px);
}

</style>

<div class="sidebar">
    <h2>Menu</h2>



    
    <?php if ($is_admin): ?>

        <?php
        $main_pages = ['dashboard.php', 'notifications.php'];
        $inventory_pages = [
            'inventory.php', 'edit_product.php', 'delete_product.php', 'product_history.php',
            'add_product.php',
            'categories.php', 'add_category.php', 'edit_category.php', 'delete_category.php',
            'suppliers.php', 'add_supplier.php', 'edit_supplier.php', 'delete_supplier.php',
            'stock_in.php',
            'inventory_adjustment.php',
            'inventory_logs.php',
            'reorder_list.php',
            'inventory_forecast.php'
        ];
        $service_pages = [
            'services.php', 'add_service.php', 'edit_service.php', 'delete_service.php',
            'service_request.php', 'service_request_receipt.php',
            'service_products.php', 'edit_service_product.php', 'delete_service_product.php',
            'log_service.php',
            'usage_logs.php'
        ];
        $report_pages = ['reports.php', 'audit_logs.php'];
        ?>

        <button type="button" class="menu-toggle <?php echo is_toggle_active($main_pages, $current_page); ?>" onclick="toggleMenu('mainMenu')">
            Main <span class="arrow">›</span>
        </button>
        <div id="mainMenu" class="menu-group <?php echo is_group_open($main_pages, $current_page); ?>">
            <a href="dashboard.php" class="<?php echo is_active('dashboard.php', $current_page); ?>"><i class="fa-solid fa-gauge"></i> Dashboard</a>
            <a href="notifications.php" class="<?php echo is_active('notifications.php', $current_page); ?>"><i class="fa-solid fa-bell"></i> Notifications</a>
        </div>

        <button type="button" class="menu-toggle <?php echo is_toggle_active($inventory_pages, $current_page); ?>" onclick="toggleMenu('inventoryMenu')">
            Inventory Management <span class="arrow">›</span>
        </button>
        <div id="inventoryMenu" class="menu-group <?php echo is_group_open($inventory_pages, $current_page); ?>">
            <a href="inventory.php" class="<?php echo is_active(['inventory.php', 'edit_product.php', 'delete_product.php', 'product_history.php'], $current_page); ?>"><i class="fa-solid fa-boxes-stacked"></i> Inventory</a>
            <a href="add_product.php" class="<?php echo is_active('add_product.php', $current_page); ?>"><i class="fa-solid fa-plus"></i> Add Product</a>
            <a href="categories.php" class="<?php echo is_active(['categories.php', 'add_category.php', 'edit_category.php', 'delete_category.php'], $current_page); ?>"><i class="fa-solid fa-tags"></i> Categories</a>
            <a href="suppliers.php" class="<?php echo is_active(['suppliers.php', 'add_supplier.php', 'edit_supplier.php', 'delete_supplier.php'], $current_page); ?>"><i class="fa-solid fa-truck-field"></i> Suppliers</a>
            <a href="stock_in.php" class="<?php echo is_active('stock_in.php', $current_page); ?>"><i class="fa-solid fa-box-open"></i> Stock In / Restock</a>
            <a href="reorder_list.php" class="<?php echo is_active('reorder_list.php', $current_page); ?>"><i class="fa-solid fa-list-check"></i> Reorder List</a>
            <a href="inventory_adjustment.php" class="<?php echo is_active('inventory_adjustment.php', $current_page); ?>"><i class="fa-solid fa-sliders"></i> Inventory Adjustment</a>
            <a href="inventory_logs.php" class="<?php echo is_active('inventory_logs.php', $current_page); ?>"><i class="fa-solid fa-clock-rotate-left"></i> Inventory History</a>
            <a href="inventory_forecast.php" class="<?php echo is_active('inventory_forecast.php', $current_page); ?>"><i class="fa-solid fa-chart-line"></i> Inventory Forecast</a>
        </div>

        <button type="button" class="menu-toggle <?php echo is_toggle_active($service_pages, $current_page); ?>" onclick="toggleMenu('serviceMenu')">
            Service Management <span class="arrow">›</span>
        </button>
        <div id="serviceMenu" class="menu-group <?php echo is_group_open($service_pages, $current_page); ?>">
            <a href="services.php" class="<?php echo is_active(['services.php', 'add_service.php', 'edit_service.php', 'delete_service.php'], $current_page); ?>"><i class="fa-solid fa-spray-can-sparkles"></i> Services</a>
            <a href="service_products.php" class="<?php echo is_active(['service_products.php', 'edit_service_product.php', 'delete_service_product.php'], $current_page); ?>"><i class="fa-solid fa-link"></i> Assign Products</a>
            <a href="log_service.php" class="<?php echo is_active('log_service.php', $current_page); ?>"><i class="fa-solid fa-clipboard-check"></i> Log Service Usage</a>
            <a href="usage_logs.php" class="<?php echo is_active('usage_logs.php', $current_page); ?>"><i class="fa-solid fa-file-lines"></i> Usage Logs</a>
            <a href="inactive_records.php" class="<?php echo $current_page == 'inactive_records.php' ? 'active' : ''; ?>"><i class="fa-solid fa-box-archive"></i> Inactive Records</a>
        </div>

        <button type="button" class="menu-toggle <?php echo is_toggle_active($report_pages, $current_page); ?>" onclick="toggleMenu('reportMenu')">
            Reports & Audit <span class="arrow">›</span>
        </button>
        <div id="reportMenu" class="menu-group <?php echo is_group_open($report_pages, $current_page); ?>">
            <a href="reports.php" class="<?php echo is_active('reports.php', $current_page); ?>"><i class="fa-solid fa-chart-pie"></i> Reports</a>
            <a href="audit_logs.php" class="<?php echo is_active('audit_logs.php', $current_page); ?>"><i class="fa-solid fa-shield-halved"></i> Audit Logs</a>
            <a href="deleted_records.php" class="<?php echo $current_page == 'deleted_records.php' ? 'active' : ''; ?>"><i class="fa-solid fa-trash-can-arrow-up"></i> Deleted Records</a>        
    </div>

    <?php else: ?>

        <?php
        $staff_main_pages = ['dashboard.php', 'notifications.php'];
        $staff_operation_pages = [
            'inventory.php',
            'services.php',
            'service_request.php', 'service_request_receipt.php',
            'log_service.php',
            'usage_logs.php',
            'inventory_forecast.php'
        ];
        ?>

        <button type="button" class="menu-toggle <?php echo is_toggle_active($staff_main_pages, $current_page); ?>" onclick="toggleMenu('staffMainMenu')">
            Main <span class="arrow">›</span>
        </button>
        <div id="staffMainMenu" class="menu-group <?php echo is_group_open($staff_main_pages, $current_page); ?>">
            <a href="dashboard.php" class="<?php echo is_active('dashboard.php', $current_page); ?>"><i class="fa-solid fa-gauge"></i> Dashboard</a>
            <a href="notifications.php" class="<?php echo is_active('notifications.php', $current_page); ?>"><i class="fa-solid fa-bell"></i> Notifications</a>
        </div>

        <button type="button" class="menu-toggle <?php echo is_toggle_active($staff_operation_pages, $current_page); ?>" onclick="toggleMenu('staffOperationMenu')">
            Operations <span class="arrow">›</span>
        </button>
        <div id="staffOperationMenu" class="menu-group <?php echo is_group_open($staff_operation_pages, $current_page); ?>">
            <a href="log_service.php" class="<?php echo is_active('log_service.php', $current_page); ?>"><i class="fa-solid fa-clipboard-check"></i> Log Service Usage</a>
            <a href="services.php" class="<?php echo is_active('services.php', $current_page); ?>"><i class="fa-solid fa-spray-can-sparkles"></i> Services</a>
            <a href="usage_logs.php" class="<?php echo is_active('usage_logs.php', $current_page); ?>"><i class="fa-solid fa-file-lines"></i> Usage Logs</a>
            <a href="inventory.php" class="<?php echo is_active('inventory.php', $current_page); ?>"><i class="fa-solid fa-boxes-stacked"></i> Inventory</a>
            <a href="inventory_forecast.php" class="<?php echo is_active('inventory_forecast.php', $current_page); ?>"><i class="fa-solid fa-chart-line"></i> Inventory Forecast</a>
        </div>

    <?php endif; ?>

    <button type="button" class="menu-toggle active" onclick="toggleMenu('accountMenu')">
        Account <span class="arrow">›</span>
    </button>
    <div id="accountMenu" class="menu-group open">
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>
</div>

<script>
function toggleMenu(id) {
    const group = document.getElementById(id);
    const button = group.previousElementSibling;

    group.classList.toggle('open');
    button.classList.toggle('active');
}
</script>