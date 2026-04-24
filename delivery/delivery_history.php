<?php
session_start();

// Check if delivery is logged in
if (!isset($_SESSION['delivery_loggedin']) || !$_SESSION['delivery_loggedin']) {
    header('Location: delivery_login.php');
    exit;
}

include '../db_connection.php';

$delivery_id = $_SESSION['delivery_id'];
$delivery_name = $_SESSION['delivery_name'];
$delivery_email = $_SESSION['delivery_email'];

// Get all completed orders for this delivery - support both Arabic and English
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_status IN ('مكتمل', 'Completed') ORDER BY order_date DESC LIMIT 50");
$stmt->execute();
$completedOrders = $stmt->get_result();

// Calculate total earnings (delivery fee only) - support both Arabic and English
$stmt = $conn->prepare("SELECT SUM(delivery_fee) as total FROM orders WHERE order_status IN ('مكتمل', 'Completed') AND delivery_fee > 0");
$stmt->execute();
$totalResult = $stmt->get_result();
$totalRow = $totalResult->fetch_assoc();
$totalEarnings = $totalRow['total'] ?? 0;

// Get today's earnings
$today = date('Y-m-d');
$stmt = $conn->prepare("SELECT SUM(delivery_fee) as today_earnings FROM orders WHERE DATE(order_date) = ? AND order_status IN ('مكتمل', 'Completed') AND delivery_fee > 0");
$stmt->bind_param('s', $today);
$stmt->execute();
$todayResult = $stmt->get_result();
$todayRow = $todayResult->fetch_assoc();
$todayEarnings = $todayRow['today_earnings'] ?? 0;

// Get today's completed deliveries count
$stmt = $conn->prepare("SELECT COUNT(*) as today_count FROM orders WHERE DATE(order_date) = ? AND order_status IN ('مكتمل', 'Completed')");
$stmt->bind_param('s', $today);
$stmt->execute();
$todayCountResult = $stmt->get_result();
$todayCountRow = $todayCountResult->fetch_assoc();
$todayDeliveries = $todayCountRow['today_count'] ?? 0;

// Get total completed deliveries count
$stmt = $conn->prepare("SELECT COUNT(*) as total_count FROM orders WHERE order_status IN ('مكتمل', 'Completed')");
$stmt->execute();
$totalCountResult = $stmt->get_result();
$totalCountRow = $totalCountResult->fetch_assoc();
$totalDeliveries = $totalCountRow['total_count'] ?? 0;

$conn->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سجل التوصيلات | Hummy Cake</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        :root {
            --primary: #FF7B54;
            --secondary: #FFB26B;
            --dark: #2D3748;
            --light-gray: #F7FAFC;
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.8);
            --success: #38A169;
            --info: #3182CE;
            --warning: #DD6B20;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Cairo', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #F8FAFC 0%, #EFF6FF 100%);
            min-height: 100vh;
            color: var(--dark);
            padding-bottom: 50px;
            overflow-x: hidden;
        }

        /* Glassmorphic Navbar (Same as Dashboard for consistency) */
        .top-nav {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
        }

        .top-nav .container { display: flex; justify-content: space-between; align-items: center; }

        .logo {
            font-size: 24px;
            font-weight: 800;
            text-decoration: none;
            background: linear-gradient(135deg, var(--primary), var(--warning));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo i { font-size: 28px; color: var(--primary); -webkit-text-fill-color: initial; }

        .nav-actions { display: flex; gap: 10px; }

        .nav-btn {
            padding: 10px 20px;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            border: none;
        }

        .btn-home { background: rgba(255, 123, 84, 0.1); color: var(--primary); }
        .btn-home:hover { background: rgba(255, 123, 84, 0.2); color: var(--primary); transform: translateY(-2px); }

        .btn-history { background: var(--info); color: white; box-shadow: 0 5px 15px rgba(49, 130, 206, 0.3); }
        .btn-logout { background: rgba(229, 62, 62, 0.1); color: #E53E3E; }
        .btn-logout:hover { background: rgba(229, 62, 62, 0.2); color: #E53E3E; transform: translateY(-2px); }

        /* Stats Cards (Luxury style) */
        .stats-container { margin-top: 40px; margin-bottom: 40px; }
        
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(0,0,0,0.02);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .stat-card::after {
            content: ''; position: absolute; top: -50%; right: -50%; width: 100px; height: 100px;
            background: radial-gradient(circle, rgba(255,255,255,0.8) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%; opacity: 0; transition: all 0.5s ease;
        }

        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0, 0, 0, 0.06); }
        .stat-card:hover::after { opacity: 1; transform: scale(3); top: -20%; right: -20%; }

        .stat-icon {
            width: 60px; height: 60px; border-radius: 16px; display: flex; align-items: center; justify-content: center;
            font-size: 24px; color: white; flex-shrink: 0; box-shadow: inset 0 -3px 0 rgba(0,0,0,0.1);
        }

        .icon-total { background: linear-gradient(135deg, #805AD5, #B794F4); }
        .icon-today { background: linear-gradient(135deg, #38A169, #68D391); }
        .icon-count { background: linear-gradient(135deg, var(--info), #63B3ED); }
        .icon-count-tdy { background: linear-gradient(135deg, var(--warning), #F6AD55); }

        .stat-info h3 { font-size: 26px; font-weight: 800; margin: 0; color: var(--dark); }
        .stat-info p { margin: 5px 0 0 0; color: #718096; font-size: 14px; font-weight: 600; }

        /* Modern Data Grid */
        .table-container {
            background: white;
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(0,0,0,0.02);
            position: relative;
        }

        .table-title {
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--dark);
        }

        .table-title .icon-bg {
            display: flex; align-items: center; justify-content: center; width: 45px; height: 45px;
            border-radius: 14px; background: linear-gradient(135deg, var(--info), #4299E1);
            color: white; font-size: 20px; box-shadow: 0 10px 20px rgba(49, 130, 206, 0.2);
        }

        .custom-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        
        .custom-table th {
            background: #F7FAFC;
            padding: 18px 15px;
            text-align: right;
            font-weight: 700;
            color: #4A5568;
            font-size: 14px;
            border-bottom: 2px solid #E2E8F0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .custom-table th:first-child { border-top-right-radius: 12px; border-bottom-right-radius: 12px; }
        .custom-table th:last-child { border-top-left-radius: 12px; border-bottom-left-radius: 12px; }

        .custom-table td {
            padding: 18px 15px;
            border-bottom: 1px dashed #E2E8F0;
            color: #2D3748;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.2s ease;
        }

        .custom-table tbody tr:hover td {
            background: #F7FAFC;
        }

        .custom-table tbody tr:last-child td { border-bottom: none; }

        .badge-completed {
            background: #C6F6D5;
            color: #22543D;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 2px 5px rgba(56, 161, 105, 0.1);
        }

        .order-hash {
            background: #EDF2F7;
            padding: 4px 10px;
            border-radius: 8px;
            color: #4A5568;
            font-family: monospace;
            font-weight: 800;
        }

        .fee-highlight {
            color: var(--primary);
            font-weight: 800;
            background: rgba(255, 123, 84, 0.1);
            padding: 4px 10px;
            border-radius: 8px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        
        .empty-state i {
            font-size: 70px;
            color: #E2E8F0;
            margin-bottom: 25px;
        }

        .empty-state h4 { color: #4A5568; font-weight: 800; font-size: 22px; }

        @media (max-width: 991px) {
            .custom-table { display: block; overflow-x: auto; white-space: nowrap; }
        }

        @media (max-width: 768px) {
            .top-nav .container { flex-wrap: wrap; gap: 15px; }
            .nav-actions { width: 100%; display: flex; justify-content: space-between; gap: 10px; }
            .nav-actions a { flex: 1; justify-content: center; }
        }
    </style>
</head>
<body>

<!-- Top Navigation -->
<nav class="top-nav animate__animated animate__fadeInDown">
    <div class="container">
        <a href="delivery_dashboard.php" class="logo">
            <i class="fas fa-shipping-fast"></i>
            Hummy Delivery
        </a>
        
        <div class="nav-actions">
            <a href="delivery_dashboard.php" class="nav-btn btn-home">
                <i class="fas fa-home"></i> التحكم
            </a>
            <a href="delivery_history.php" class="nav-btn btn-history">
                <i class="fas fa-clock"></i> السجل
            </a>
            <a href="logout.php" class="nav-btn btn-logout">
                <i class="fas fa-sign-out-alt"></i> خروج
            </a>
        </div>
    </div>
</nav>

<!-- Stats -->
<div class="container stats-container">
    <div class="row g-4">
        <div class="col-xl-3 col-md-6 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
            <div class="stat-card">
                <div class="stat-icon icon-total"><i class="fas fa-money-check-alt"></i></div>
                <div class="stat-info">
                    <h3><?php echo number_format($totalEarnings); ?></h3>
                    <p>إجمالي الأرباح المشتركة (ريال)</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
            <div class="stat-card">
                <div class="stat-icon icon-today"><i class="fas fa-hand-holding-usd"></i></div>
                <div class="stat-info">
                    <h3><?php echo number_format($todayEarnings); ?></h3>
                    <p>أرباح التوصيل لليوم</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
            <div class="stat-card">
                <div class="stat-icon icon-count"><i class="fas fa-boxes"></i></div>
                <div class="stat-info">
                    <h3><?php echo $totalDeliveries; ?></h3>
                    <p>إجمالي الشحنات المنجزة</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
            <div class="stat-card">
                <div class="stat-icon icon-count-tdy"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-info">
                    <h3><?php echo $todayDeliveries; ?></h3>
                    <p>إنجازات اليوم</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- History Table -->
<div class="container animate__animated animate__fadeIn" style="animation-delay: 0.5s;">
    <div class="table-container">
        <h3 class="table-title">
            <div class="icon-bg"><i class="fas fa-list-ul"></i></div>
            الأرشيف وسجل التوصيلات المكتملة
        </h3>
        
        <?php if ($completedOrders->num_rows > 0): ?>
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>رقم الطلب</th>
                        <th>العميل</th>
                        <th>رقم التواصل</th>
                        <th>عنوان التوصيل</th>
                        <th>إجمالي الفاتورة</th>
                        <th>ربحك الخاص</th>
                        <th>التاريخ</th>
                        <th>حالة الطلب</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $completedOrders->fetch_assoc()): ?>
                        <tr>
                            <td><span class="order-hash">#<?php echo $order['order_id']; ?></span></td>
                            <td><i class="fas fa-user-circle text-muted ms-1"></i> <?php echo $order['firstName'] . ' ' . $order['lastName']; ?></td>
                            <td><span dir="ltr"><?php echo $order['phone']; ?></span></td>
                            <td><i class="fas fa-map-marker-alt text-danger ms-1"></i> <?php echo $order['address']; ?></td>
                            <td><?php echo number_format($order['grand_total']); ?> ريال</td>
                            <td><span class="fee-highlight"><?php echo number_format($order['delivery_fee']); ?> ريال</span></td>
                            <td><span class="text-muted"><i class="far fa-calendar-alt ms-1"></i> <?php echo date('Y-m-d', strtotime($order['order_date'])); ?></span></td>
                            <td>
                                <span class="badge-completed">
                                    <i class="fas fa-check-circle"></i> مكتمل
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <h4>سجلك نظيف تماماً!</h4>
                <p class="text-muted">لم تقم بإنجاز أي طلبات حتى الآن، ابدأ بتوصيل الطلبات الأولى ليظهر السجل هنا.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
