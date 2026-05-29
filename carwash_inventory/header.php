<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user_display = $_SESSION['full_name'] ?? 'Admin User';

include_once 'config.php';

$notification_items = [];
$notification_count = 0;

if (isset($conn)) {
    $notification_sql = "
        SELECT product_name, quantity_in_stock, reorder_level
        FROM products
        WHERE status = 'Active'
        AND deleted_status = 'Active'
        AND quantity_in_stock <= reorder_level
        ORDER BY quantity_in_stock ASC
        LIMIT 3
    ";

    $notification_result = $conn->query($notification_sql);

    if ($notification_result && $notification_result->num_rows > 0) {
        while ($row = $notification_result->fetch_assoc()) {
            $notification_items[] = $row;
        }
    }

    $notification_count = count($notification_items);
}

$notification_count = 0;

$notification_sql = "
    SELECT COUNT(*) AS total_alerts
    FROM products
    WHERE status = 'Active'
    AND deleted_status = 'Active'
    AND quantity_in_stock <= reorder_level
";

$notification_result = $conn->query($notification_sql);

if ($notification_result) {
    $notification_row = $notification_result->fetch_assoc();
    $notification_count = (int)$notification_row['total_alerts'];
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Carwash Inventory System</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>

.stock-alert-grid {
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 18px;
    width: 100%;
    margin-top: 20px;
}

.stock-alert-grid .card {
    width: 100%;
    min-height: 145px;
}

@media (max-width: 1000px) {
    .stock-alert-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }
}

@media (max-width: 700px) {
    .stock-alert-grid {
        grid-template-columns: 1fr !important;
    }
}

.status-badge {
    display: inline-flex;
    align-items: center;

    padding: 6px 12px;

    border-radius: 999px;

    font-size: 12px;
    font-weight: 800;

    text-transform: uppercase;

    letter-spacing: .3px;
}

.badge-active {
    background: rgba(22,163,74,.15);
    color: #22c55e;
}

.badge-low {
    background: rgba(245,158,11,.15);
    color: #f59e0b;
}

.badge-critical {
    background: rgba(249,115,22,.15);
    color: #f97316;
}

.badge-out {
    background: rgba(220,38,38,.15);
    color: #ef4444;
}

.badge-inactive {
    background: rgba(148,163,184,.15);
    color: #cbd5e1;
}

.badge-deleted {
    background: rgba(75,85,99,.20);
    color: #9ca3af;
}

.notif-bell {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;

    width: 42px;
    height: 42px;

    border-radius: 14px;
    background: rgba(255,255,255,0.10);
    color: #ffffff;
    text-decoration: none;

    margin-right: 12px;
    transition: 0.2s ease;
}

.notif-bell:hover {
    background: rgba(37, 99, 235, 0.35);
    transform: translateY(-2px);
}

.notif-bell i {
    font-size: 18px;
}

.notif-badge {
    position: absolute;
    top: -6px;
    right: -6px;

    min-width: 20px;
    height: 20px;
    padding: 0 6px;

    border-radius: 999px;
    background: #dc2626;
    color: #ffffff;

    font-size: 11px;
    font-weight: 900;

    display: flex;
    align-items: center;
    justify-content: center;

    border: 2px solid #0f172a;
}

.system-status-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}

.status-box {
    display: flex;
    align-items: center;
    gap: 14px;

    background: rgba(15, 23, 42, 0.72);
    border: 1px solid rgba(148, 163, 184, 0.18);
    border-radius: 16px;

    padding: 18px 20px;

    box-shadow: 0 12px 30px rgba(0,0,0,0.16);
}

.status-box i {
    width: 44px;
    height: 44px;
    border-radius: 14px;

    display: grid;
    place-items: center;

    background: rgba(37, 99, 235, 0.18);
    color: #60a5fa;

    font-size: 20px;
}

.status-box strong {
    display: block;
    color: #f8fafc;
    font-size: 14px;
}

.status-box span {
    display: block;
    color: #94a3b8;
    font-size: 13px;
    margin-top: 3px;
}

@media (max-width: 900px) {
    .system-status-grid {
        grid-template-columns: 1fr;
    }
}

.hero-role {
    display: inline-flex;
    align-items: center;
    gap: 8px;

    margin-top: 15px;

    padding: 8px 14px;

    background: rgba(255,255,255,0.12);

    border-radius: 999px;

    font-size: 13px;
    font-weight: 700;

    color: #e2e8f0;
}

.hero-role i {
    color: #fbbf24;
}

.action-btn i {
    margin-right: 7px;
}

.quick-actions-grid .action-btn {
    text-align: center;
}
.brand-title {
    display: flex;
    align-items: center;
    gap: 12px;
}

.topbar-logo {
    width: 46px;
    height: 46px;
    object-fit: contain;
    background: rgba(255,255,255,0.9);
    border-radius: 14px;
    padding: 5px;
    box-shadow: 0 8px 18px rgba(0,0,0,0.18);
}

.brand-main {
    font-size: 22px;
    font-weight: 800;
    line-height: 1;
}

.brand-sub {
    font-size: 12px;
    opacity: 0.85;
    margin-top: 4px;
}

.notification-toast {
    position: fixed;
    top: 90px;
    right: 24px;
    width: 320px;
    background: #ffffff;
    border-left: 5px solid var(--danger);
    border-radius: 14px;
    box-shadow: 0 18px 35px rgba(15, 23, 42, 0.18);
    padding: 16px;
    z-index: 9999;
    animation: toastSlideFade 6s ease forwards;
}

.notification-toast h4 {
    margin: 0 0 8px;
    color: var(--text);
}

.notification-toast p {
    margin: 4px 0;
    color: var(--muted);
    font-size: 14px;
}

.notification-toast a {
    display: inline-block;
    margin-top: 10px;
    color: var(--primary);
    font-weight: bold;
    text-decoration: none;
}

@keyframes toastSlideFade {
    0% {
        opacity: 0;
        transform: translateX(40px);
    }
    12% {
        opacity: 1;
        transform: translateX(0);
    }
    80% {
        opacity: 1;
        transform: translateX(0);
    }
    100% {
        opacity: 0;
        transform: translateX(40px);
        pointer-events: none;
    }
}

        * {
            box-sizing: border-box;
        }

        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --sidebar: #0f172a;
            --sidebar-hover: #1e293b;            
            --bg: #f8fafc;
            --card: #ffffff;
            --text: #1f2937;
            --muted: #6b7280;
            --border: #e5e7eb;
            --success: #16a34a;
            --danger: #dc2626;
            --warning: #f59e0b;
            --shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
            --radius: 14px;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        .topbar {
            height: 70px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            box-shadow: 0 4px 16px rgba(37, 99, 235, 0.2);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .topbar .title {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 0.3px;
        }

        .topbar .user-area {
            font-size: 14px;
            background: rgba(255,255,255,0.15);
            padding: 8px 14px;
            border-radius: 999px;
        }

        .main-wrapper {
            display: flex;
            min-height: calc(100vh - 70px);
            width: 100%;
        }

        .sidebar {
            width: 250px;
            background: var(--sidebar);
            color: white;
            padding-top: 24px;
            flex-shrink: 0;
            box-shadow: 2px 0 12px rgba(0,0,0,0.05);
        }

        .sidebar h2 {
            text-align: center;
            font-size: 20px;
            margin: 0 0 24px;
            padding: 0 14px;
            color: #e2e8f0;
        }

        .sidebar a {
            display: block;
            color: #e5e7eb;
            text-decoration: none;
            padding: 14px 20px;
            margin: 4px 12px;
            border-radius: 10px;
            transition: background 0.2s ease, transform 0.2s ease, color 0.2s ease;
        }

        .sidebar a:hover {
            background: var(--sidebar-hover);
            color: white;
            transform: translateX(4px);
        }
        .sidebar a.active {
         background: var(--primary);
         color: white;
         transform: translateX(4px);
         box-shadow: 0 6px 14px rgba(37, 99, 235, 0.25);
         }
         .menu-toggle {
    width: calc(100% - 20px);
    margin: 10px;
    padding: 12px 16px;
    background: transparent;
    color: #7f8aa3;
    border: none;
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-radius: 10px;
}

.menu-toggle:hover {
    background: rgba(37, 99, 235, 0.12);
    color: #ffffff;
}

.menu-toggle .arrow {
    transition: transform 0.2s ease;
    font-size: 12px;
}

.menu-toggle.active .arrow {
    transform: rotate(90deg);
}

.menu-group {
    display: none;
    margin-bottom: 8px;
}

.menu-group.open {
    display: block;
}



.content {
    flex: 1;
    width: 100%;
    max-width: none;
    padding: 32px;
    animation: fadeSlide 1.5s ease;
}

@keyframes fadeSlide {
    from {
        opacity: 0;
        transform: translateY(8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

        .page-title {
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 28px;
            font-weight: bold;
            color: var(--text);
            
        }

        .page-card {
    background: rgba(15, 23, 42, 0.72);
    border: 1px solid rgba(148, 163, 184, 0.18);
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 14px 35px rgba(0, 0, 0, 0.18);
    backdrop-filter: blur(10px);
    transition: 0.2s ease;
}

.page-card:hover {
    border-color: rgba(37, 99, 235, 0.35);
}

.page-card h3 {
    margin-top: 0;
    margin-bottom: 18px;
    font-size: 19px;
    font-weight: 800;
    color: #e5e7eb;
}

table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    overflow: hidden;
    border-radius: 14px;
}

table th {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: white;
    font-weight: 800;
    padding: 14px 16px;
    font-size: 14px;
}

table td {
    padding: 14px 16px;
    border-bottom: 1px solid rgba(148, 163, 184, 0.18);
    color: #f8fafc;
    font-size: 14px;
}

table tr:hover td {
    background: rgba(37, 99, 235, 0.08);
}

table tr:last-child td {
    border-bottom: none;
}

.compact-table th,
.compact-table td {
    padding: 11px 13px;
    font-size: 13px;
}

        .page-card {
            background: var(--card);
            padding: 22px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            margin-bottom: 24px;
            border: 1px solid rgba(229, 231, 235, 0.7);
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 24px;
        }
        .card-link {
        text-decoration: none;
        color: inherit;
        display: block;
        }

        .card-link:hover .card {
        transform: translateY(-3px);
         box-shadow: 0 14px 28px rgba(15, 23, 42, 0.14);
        }

        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
            padding: 22px;
            border-radius: var(--radius);
            color: white;
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
        }

        .card::after {
            content: "";
            position: absolute;
            right: -20px;
            top: -20px;
            width: 90px;
            height: 90px;
            background: rgba(255,255,255,0.12);
            border-radius: 50%;
        }

        .card h2 {
            margin: 0;
            font-size: 32px;
            font-weight: bold;
        }

        .card p {
            margin: 10px 0 0;
            font-size: 14px;
            opacity: 0.95;
        }

        .blue { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .green { background: linear-gradient(135deg, #22c55e, #16a34a); }
        .red { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .orange { background: linear-gradient(135deg, #f59e0b, #d97706); }

        .action-btn {
            display: inline-block;
            padding: 10px 16px;
            background: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            margin-right: 8px;
            margin-bottom: 10px;
            font-size: 14px;
            font-weight: bold;
            transition: background 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 6px 14px rgba(37, 99, 235, 0.15);
        }

        .action-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        form label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            color: var(--text);
        }

.filter-title {
    font-size: 18px;
    font-weight: 800;
    margin-bottom: 18px;
    color: #e5e7eb;
}

.filter-form {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
    align-items: end;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.filter-group label,
form label {
    font-weight: 700;
    font-size: 14px;
    color: #e5e7eb;
}

input[type="text"],
input[type="number"],
input[type="password"],
input[type="email"],
input[type="date"],
select,
textarea {
    width: 100%;
    background: rgba(15, 23, 42, 0.82);
    color: #f8fafc;
    border: 1px solid rgba(148, 163, 184, 0.35);
    border-radius: 12px;
    padding: 12px 14px;
    font-size: 14px;
    outline: none;
    transition: 0.2s ease;
}

input::placeholder,
textarea::placeholder {
    color: #94a3b8;
}

input:focus,
select:focus,
textarea:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.18);
}

textarea {
    min-height: 100px;
    resize: vertical;
}

.filter-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.form-card {
    max-width: 720px;
}

.form-grid-2 {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 18px;
}

@media (max-width: 1100px) {
    .filter-form {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 700px) {
    .filter-form,
    .form-grid-2 {
        grid-template-columns: 1fr;
    }
}
        
        input[type="text"],
        input[type="number"],
        input[type="password"],
        input[type="email"],
        select,
        textarea {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: #fff;
            font-size: 14px;
            color: var(--text);
            outline: none;
            transition: border 0.2s ease, box-shadow 0.2s ease;
            margin-bottom: 15px;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }

        textarea {
            resize: vertical;
            min-height: 90px;
        }

        button.action-btn {
            border: none;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
        }

        th, td {
            border-bottom: 1px solid var(--border);
            padding: 14px 12px;
            text-align: left;
            vertical-align: top;
            font-size: 14px;
        }

        th {
            background: var(--primary);
            color: white;
            font-weight: bold;
        }

        tr:hover td {
            background: #f8fbff;
        }

        td a {
            color: var(--primary-dark);
            text-decoration: none;
            font-weight: bold;
        }

        td a:hover {
            text-decoration: underline;
        }

        .low-stock-text {
            color: var(--danger);
            font-weight: bold;
        }

        .success-text {
            color: var(--success);
            font-weight: bold;
        }

        .warning-text {
            color: var(--warning);
            font-weight: bold;
        }

        .empty-message {
            color: var(--muted);
            font-size: 14px;
        }

        .footer {
            text-align: center;
            padding: 16px;
            background: #ffffff;
            border-top: 1px solid var(--border);
            color: var(--muted);
            font-size: 14px;
        }
        @media (min-width: 1400px) {
    .content {
        padding: 36px 48px;
    }

.dashboard-hero {
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.28), rgba(15, 23, 42, 0.95));
    border: 1px solid rgba(148, 163, 184, 0.18);
    border-radius: 20px;
    padding: 28px;
    margin-bottom: 24px;
    box-shadow: 0 18px 40px rgba(0, 0, 0, 0.22);
}

.dashboard-hero h1 {
    margin: 0 0 8px;
    font-size: 30px;
    font-weight: 900;
    color: #ffffff;
}

.dashboard-hero p {
    margin: 0;
    color: #cbd5e1;
    font-size: 15px;
}

.card {
    position: relative;
    overflow: hidden;
    border-radius: 18px;
    padding: 24px;
    min-height: 130px;
    transition: 0.22s ease;
}

.card:hover {
    transform: translateY(-4px);
    box-shadow: 0 18px 35px rgba(0, 0, 0, 0.25);
}

.card::after {
    content: "";
    position: absolute;
    width: 90px;
    height: 90px;
    right: -22px;
    bottom: -24px;
    background: rgba(255, 255, 255, 0.16);
    border-radius: 50%;
}

.card h2 {
    font-size: 34px;
    margin-bottom: 8px;
    font-weight: 900;
}

.card p {
    font-size: 14px;
    font-weight: 700;
    opacity: 0.92;
}

.dashboard-section-title {
    font-size: 20px;
    font-weight: 900;
    color: #e5e7eb;
    margin: 26px 0 14px;
}

.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 12px;
}

    .dashboard-grid {
        grid-template-columns: minmax(0, 2.2fr) minmax(380px, 1fr);
    }


    
    .page-card {
        padding: 26px;
    }
}
        @media (max-width: 1100px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }

    .cards {
        grid-template-columns: repeat(2, 1fr);
    }

    .sidebar {
        width: 220px;
    }
}

@media (max-width: 768px) {
    .main-wrapper {
        flex-direction: column;
    }

    .sidebar {
        width: 100%;
    }

    .cards {
        grid-template-columns: 1fr;
    }

    .content {
        padding: 18px;
    }
}

        @media (max-width: 768px) {
            .main-wrapper {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                padding-bottom: 16px;
            }

            .sidebar a {
                margin: 4px 16px;
            }

            .cards {
                grid-template-columns: 1fr;
            }

            .content {
                padding: 18px;
            }

            .topbar {
                padding: 0 16px;
            }

            .topbar .title {
                font-size: 20px;
            }

            .topbar .user-area {
                font-size: 12px;
            }
            .dashboard-grid {
    display: grid;
    grid-template-columns: minmax(0, 2fr) minmax(320px, 1fr);
    gap: 24px;
    margin-bottom: 24px;
    width: 100%;
}

.page-card {
    width: 100%;
}

.cards {
    display: grid;
    grid-template-columns: repeat(4, minmax(180px, 1fr));
    gap: 18px;
    margin-bottom: 24px;
    width: 100%;
}

.dashboard-side-stack {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.quick-actions-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.compact-table th,
.compact-table td {
    padding: 10px 12px;
    font-size: 13px;
}

@media (max-width: 1100px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
}
.alert {
    padding: 14px 16px;
    border-radius: 12px;
    margin-top: 15px;
    margin-bottom: 10px;
    font-weight: bold;
    border: 1px solid transparent;
}

.alert-success {
    background: rgba(22, 163, 74, 0.12);
    color: #22c55e;
    border-color: rgba(34, 197, 94, 0.25);
}

.alert-error {
    background: rgba(220, 38, 38, 0.12);
    color: #ef4444;
    border-color: rgba(239, 68, 68, 0.25);
}

.alert-warning {
    background: rgba(245, 158, 11, 0.12);
    color: #f59e0b;
    border-color: rgba(245, 158, 11, 0.25);
    
}
.chart-list {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.chart-item {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.chart-top {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    font-size: 14px;
    font-weight: bold;
}

.chart-label {
    color: var(--text);
}

.chart-value {
    color: var(--muted);
}

.chart-bar-wrap {
    width: 100%;
    height: 12px;
    background: rgba(148, 163, 184, 0.15);
    border-radius: 999px;
    overflow: hidden;
}

.chart-bar {
    height: 100%;
    border-radius: 999px;
    background: linear-gradient(90deg, var(--primary), #60a5fa);
}
.chart-card {
    display: flex;
    flex-direction: column;
    align-items: center;

    min-height: auto !important;
    padding: 24px;
}

.chart-card canvas {
    width: 100% !important;
    max-width: 500px;

    max-height: 280px !important;
    height: 280px !important;
}
.chart-stock-layout {
    display: grid;
    grid-template-columns: minmax(360px, 1.2fr) minmax(280px, 0.8fr);
    gap: 22px;
    align-items: stretch;
    margin-bottom: 24px;
}

.chart-stock-layout .dashboard-chart {
    max-width: none;
    margin: 0;
}

.side-stock-alerts {
    display: grid !important;
    grid-template-columns: 1fr !important;
    gap: 16px;
    margin-top: 0;
}

.side-stock-alerts .card {
    min-height: 125px;
}

@media (max-width: 1100px) {
    .chart-stock-layout {
        grid-template-columns: 1fr;
    }

    .side-stock-alerts {
        grid-template-columns: repeat(3, 1fr) !important;
    }
}

@media (max-width: 750px) {
    .side-stock-alerts {
        grid-template-columns: 1fr !important;
    }
}

@media print {
    .sidebar,
    .topbar,
    .footer,
    .action-btn,
    .quick-actions-grid {
        display: none !important;
    }

    body {
        background: white !important;
        color: black !important;
    }

    .main-wrapper,
    .content {
        display: block !important;
        width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .page-title {
        color: black !important;
        margin-bottom: 20px !important;
        
    }

    .page-card {
        background: white !important;
        box-shadow: none !important;
        border: 1px solid #ccc !important;
        margin-bottom: 20px !important;
        break-inside: avoid;
    }

    .cards {
        display: grid !important;
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 12px !important;
    }

    .card {
        color: black !important;
        background: white !important;
        border: 1px solid #ccc !important;
        box-shadow: none !important;
    }

    .card::after {
        display: none !important;
    }

    table {
        width: 100% !important;
        border-collapse: collapse !important;
        background: white !important;
    }

    th, td {
        border: 1px solid #999 !important;
        color: black !important;
        background: white !important;
        padding: 8px !important;
    }

    .low-stock-text,
    .success-text,
    .warning-text {
        color: black !important;
    }

    a {
        color: black !important;
        text-decoration: none !important;
    }
}
            
        }
        .breadcrumb-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 12px 16px;
    margin-bottom: 18px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    box-shadow: var(--shadow);
}

.breadcrumb-path {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--muted);
    font-size: 14px;
}

.breadcrumb-path a,
.back-link {
    color: var(--primary);
    text-decoration: none;
    font-weight: bold;
}

.breadcrumb-path a:hover,
.back-link:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .breadcrumb-card {
        flex-direction: column;
        align-items: flex-start;
    }
}
.filter-form {
    display: grid;
    grid-template-columns: repeat(4, minmax(180px, 1fr));
    gap: 16px;
    align-items: end;
}

.filter-group {
    display: flex;
    flex-direction: column;
}

.filter-actions {
    display: flex;
    gap: 10px;
    align-items: end;
}

.filter-title {
    margin-top: 0;
    margin-bottom: 18px;
}

@media (max-width: 900px) {
    .filter-form {
        grid-template-columns: 1fr;
    }

    .filter-actions {
        flex-wrap: wrap;
    }
}
.action-group {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.btn-sm {
    display: inline-block;
    padding: 7px 11px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: 0.2s ease;
}

.btn-sm:hover {
    transform: translateY(-1px);
    opacity: 0.9;
}

.btn-view {
    background: #2563eb;
    color: white;
}

.btn-edit {
    background: #f59e0b;
    color: white;
}

.btn-deactivate {
    background: #6b7280;
    color: white;
}

.btn-delete {
    background: #dc2626;
    color: white;
}

.btn-restore {
    background: #16a34a;
    color: white;
}

input[type="date"] {
    background: var(--card);
    color: var(--text);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 11px 12px;
    font-size: 14px;
    outline: none;
}

input[type="date"]:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
}

.sidebar a {
    display: flex;
    align-items: center;
    gap: 11px;
}

.sidebar a i {
    width: 18px;
    text-align: center;
    font-size: 14px;
    opacity: 0.9;
}

.menu-toggle {
    border: 1px solid transparent;
}

.menu-toggle.active {
    background: rgba(37, 99, 235, 0.12);
    color: #ffffff;
    border-color: rgba(37, 99, 235, 0.18);
}

.menu-toggle:hover {
    background: rgba(37, 99, 235, 0.18);
}

.menu-group.open {
    animation: menuFade 0.18s ease;
}

@keyframes menuFade {
    from {
        opacity: 0;
        transform: translateY(-4px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.card-icon {
    font-size: 26px;
    margin-bottom: 12px;
    opacity: 0.9;
    display: block;
}

.card h2 {
    margin-top: 0;
}

.footer {
    margin-top: 40px;
    padding: 18px 24px;
    text-align: center;
    color: #94a3b8;
    font-size: 13px;
    border-top: 1px solid rgba(148, 163, 184, 0.18);
    background: rgba(15, 23, 42, 0.55);
}

.content {
    padding-bottom: 40px;
}

.page-title {
    letter-spacing: -0.5px;
}

.page-card + .page-card {
    margin-top: 24px;
}
.dashboard-chart {
    max-width: 700px;
    margin: 0 auto 24px auto;
}
.dashboard-chart {
    max-width: 700px;
    margin: 0 auto 24px auto;
}
.chart-stock-layout {
    display: grid !important;
    grid-template-columns: 1.5fr 0.8fr;
    gap: 22px !important;
    align-items: stretch !important;
    width: 100% !important;
    margin-bottom: 24px !important;
}

.chart-stock-layout .dashboard-chart {
    max-width: none !important;
    margin: 0 !important;
}

.side-stock-alerts {
    display: grid !important;
    grid-template-columns: 1fr !important;
    gap: 16px !important;
    width: 100% !important;
}

.side-stock-alerts .card {
    width: 100% !important;
    min-height: 125px !important;
}

.chart-card canvas {
    width: 100% !important;
    max-width: 550px !important;
    height: 340px !important;
    max-height: 340px !important;
}
.dashboard-chart {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
@media (max-width: 1100px) {
    .chart-stock-layout {
        grid-template-columns: 1fr !important;
    }

    .side-stock-alerts {
        grid-template-columns: repeat(3, 1fr) !important;
    }
}

@media (max-width: 750px) {
    .side-stock-alerts {
        grid-template-columns: 1fr !important;
    }
}
    </style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">


</head>
<body>

<div class="topbar">

<div class="title brand-title">
    <img src="assets/Carwash Logo Circle.png" class="topbar-logo" alt="Kleen Ooto Logo">
    <div>
        <div class="brand-main">Kleen Ooto</div>
        <div class="brand-sub">Carwash Inventory Management System</div>
    </div>
</div>

<div class="user-area">
    <?php if ($notification_count > 0): ?>
        <a href="notifications.php" style="color:white; text-decoration:none; margin-right:12px;">
            🔔 <?php echo $notification_count; ?>
        </a>
    <?php endif; ?>

    <?php echo htmlspecialchars($user_display); ?>

<?php if (!empty($notification_items)): ?>
    <div class="notification-toast">
        <h4>Inventory Alert</h4>

        <?php foreach ($notification_items as $item): ?>
            <p>
                <?php echo htmlspecialchars($item['product_name']); ?>
                — Stock: <?php echo (int)$item['quantity_in_stock']; ?>
            </p>
        <?php endforeach; ?>

        <?php if ($notification_count > count($notification_items)): ?>
            <p>+<?php echo $notification_count - count($notification_items); ?> more alerts</p>
        <?php endif; ?>

        <a href="notifications.php" class="notif-bell">
    <i class="fa-solid fa-bell"></i>

    <?php if ($notification_count > 0): ?>
        <span class="notif-badge"><?php echo $notification_count; ?></span>
    <?php endif; ?>
</a>
    </div>
<?php endif; ?>

</div>
</div>

<div class="main-wrapper">