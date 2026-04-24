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

// Get ALL available orders (unassigned)
$stmt = $conn->prepare("SELECT * FROM orders WHERE delivery_id IS NULL AND order_status IN ('Pending', 'قيد الانتظار', 'Processing') ORDER BY order_date DESC");
$stmt->execute();
$available_orders = $stmt->get_result();

// Get MY current active orders (assigned to me, not completed)
$stmt2 = $conn->prepare("SELECT * FROM orders WHERE delivery_id = ? AND order_status = 'On the way' ORDER BY order_date DESC");
$stmt2->bind_param('i', $delivery_id);
$stmt2->execute();
$my_active_orders = $stmt2->get_result();

// Get pending deliveries count - support both Arabic and English
$stmt = $conn->prepare("SELECT COUNT(*) as pending FROM orders WHERE order_status IN ('Pending', 'Processing')");
$stmt->execute();
$pendingResult = $stmt->get_result();
$pendingRow = $pendingResult->fetch_assoc();
$pendingDeliveries = $pendingRow['pending'] ?? 0;

// Get completed deliveries count - support both Arabic and English
$stmt = $conn->prepare("SELECT COUNT(*) as completed FROM orders WHERE order_status = 'Completed'");
$stmt->execute();
$completedResult = $stmt->get_result();
$completedRow = $completedResult->fetch_assoc();
$completedDeliveries = $completedRow['completed'] ?? 0;

// Get in-progress deliveries count
$stmt = $conn->prepare("SELECT COUNT(*) as in_progress FROM orders WHERE order_status = 'On the way'");
$stmt->execute();
$inProgressResult = $stmt->get_result();
$inProgressRow = $inProgressResult->fetch_assoc();
$inProgressDeliveries = $inProgressRow['in_progress'] ?? 0;

// Get today's deliveries - support both Arabic and English
$today = date('Y-m-d');
$stmt = $conn->prepare("SELECT COUNT(*) as today FROM orders WHERE DATE(order_date) = ? AND order_status = 'Completed'");
$stmt->bind_param('s', $today);
$stmt->execute();
$todayResult = $stmt->get_result();
$todayRow = $todayResult->fetch_assoc();
$todayDeliveries = $todayRow['today'] ?? 0;

// Get today's delivery earnings (delivery fee only)
$stmt = $conn->prepare("SELECT SUM(delivery_fee) as today_earnings FROM orders WHERE DATE(order_date) = ? AND order_status = 'Completed' AND delivery_id = ?");
$stmt->bind_param('si', $today, $delivery_id);
$stmt->execute();
$todayEarningsResult = $stmt->get_result();
$todayEarningsRow = $todayEarningsResult->fetch_assoc();
$todayEarnings = $todayEarningsRow['today_earnings'] ?? 0;

// Get total earnings (delivery fee only)
$stmt = $conn->prepare("SELECT SUM(delivery_fee) as total FROM orders WHERE order_status = 'Completed' AND delivery_id = ?");
$stmt->bind_param('i', $delivery_id);
$stmt->execute();
$totalEarningsResult = $stmt->get_result();
$totalEarningsRow = $totalEarningsResult->fetch_assoc();
$totalEarnings = $totalEarningsRow['total'] ?? 0;

$conn->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة القيادة - المندوب | Hummy Cake</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="../toast.css">
    <style>
        :root {
            --primary: #FF7B54;
            --secondary: #FFB26B;
            --dark: #2D3748;
            --light-gray: #F7FAFC;
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.8);
            --success: #38A169;
            --warning: #DD6B20;
            --info: #3182CE;
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

        /* Glassmorphic Navbar */
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

        .top-nav .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

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

        .logo i {
            font-size: 28px;
            color: var(--primary);
            -webkit-text-fill-color: initial;
            animation: bounce 2s infinite;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-details {
            text-align: right;
        }

        .user-details strong {
            display: block;
            font-size: 16px;
            font-weight: 700;
            color: var(--dark);
        }

        .user-details span {
            font-size: 13px;
            color: #718096;
            font-weight: 600;
        }

        .user-avatar {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 10px 20px rgba(255, 123, 84, 0.3);
            border: 2px solid white;
        }

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

        .btn-history {
            background: rgba(49, 130, 206, 0.1);
            color: var(--info);
        }

        .btn-history:hover {
            background: rgba(49, 130, 206, 0.2);
            color: var(--info);
            transform: translateY(-2px);
        }

        .btn-logout {
            background: rgba(229, 62, 62, 0.1);
            color: #E53E3E;
        }

        .btn-logout:hover {
            background: rgba(229, 62, 62, 0.2);
            color: #E53E3E;
            transform: translateY(-2px);
        }

        /* Stats Section */
        .stats-container {
            margin-top: 40px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid white;
            border-radius: 24px;
            padding: 25px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.03);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; right: 0; width: 5px; height: 100%;
        }

        .stat-card.pending::before { background: var(--warning); }
        .stat-card.completed::before { background: var(--success); }
        .stat-card.today::before { background: var(--info); }
        .stat-card.earnings::before { background: var(--primary); }

        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 45px rgba(0, 0, 0, 0.08);
        }

        .stat-icon {
            width: 65px;
            height: 65px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            flex-shrink: 0;
            background: white;
        }

        .stat-card.pending .stat-icon { color: var(--warning); box-shadow: 0 10px 20px rgba(221, 107, 32, 0.15); }
        .stat-card.completed .stat-icon { color: var(--success); box-shadow: 0 10px 20px rgba(56, 161, 105, 0.15); }
        .stat-card.today .stat-icon { color: var(--info); box-shadow: 0 10px 20px rgba(49, 130, 206, 0.15); }
        .stat-card.earnings .stat-icon { color: var(--primary); box-shadow: 0 10px 20px rgba(255, 123, 84, 0.15); }

        .stat-info h3 {
            font-size: 30px;
            font-weight: 800;
            margin: 0;
            color: var(--dark);
            line-height: 1.2;
        }

        .stat-info p {
            margin: 5px 0 0 0;
            color: #718096;
            font-size: 15px;
            font-weight: 600;
        }

        /* Orders Section */
        .section-title {
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--dark);
        }

        .section-title i {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            border-radius: 14px;
            color: white;
            font-size: 20px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .section-title .bg-primary-grad { background: linear-gradient(135deg, var(--info), #4299E1); }
        .section-title .bg-warning-grad { background: linear-gradient(135deg, var(--primary), var(--warning)); }

        .order-card {
            background: white;
            border-radius: 24px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(0,0,0,0.02);
            transition: all 0.3s ease;
            position: relative;
        }

        .order-card.my-active-order {
            box-shadow: 0 15px 40px rgba(49, 130, 206, 0.1);
            border: 1px solid rgba(49, 130, 206, 0.2);
        }

        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.06);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px dashed #E2E8F0;
        }

        .order-id {
            font-size: 18px;
            font-weight: 800;
            background: #EDF2F7;
            padding: 6px 15px;
            border-radius: 12px;
            color: #4A5568;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .order-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge-pending { background: #FEFCBF; color: #B7791F; }
        .badge-in-progress { background: #EBF8FF; color: #2B6CB0; }

        .order-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .detail-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .detail-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: #F7FAFC;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .order-card:hover .detail-icon {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            box-shadow: 0 8px 15px rgba(255, 123, 84, 0.2);
            transform: scale(1.05) rotate(-5deg);
        }

        .my-active-order .detail-icon { color: var(--info); }
        .my-active-order:hover .detail-icon {
            background: linear-gradient(135deg, var(--info), #4299E1);
            box-shadow: 0 8px 15px rgba(49, 130, 206, 0.2);
        }

        .detail-text {
            display: flex;
            flex-direction: column;
        }

        .detail-text span {
            font-size: 12px;
            color: #A0AEC0;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .detail-text strong {
            font-size: 15px;
            color: var(--dark);
            font-weight: 700;
        }

        .order-note {
            background: #FFF5F5;
            padding: 15px;
            border-radius: 14px;
            margin-bottom: 20px;
            color: #C53030;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            border: 1px dashed #FEB2B2;
        }

        .order-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .action-btn {
            flex: 1;
            min-width: 150px;
            padding: 14px 20px;
            border-radius: 14px;
            border: none;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .action-btn::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: -1;
            transition: opacity 0.3s ease;
            opacity: 0;
        }

        .action-btn:hover {
            transform: translateY(-3px);
            color: white;
        }

        .action-btn:hover::before { opacity: 1; }
        .action-btn:active { transform: translateY(1px); }

        .btn-start { background: linear-gradient(135deg, var(--info), #4299E1); color: white; box-shadow: 0 10px 20px rgba(49, 130, 206, 0.3); }
        .btn-start::before { background: linear-gradient(135deg, #4299E1, var(--info)); }
        
        .btn-complete { background: linear-gradient(135deg, var(--success), #48BB78); color: white; box-shadow: 0 10px 20px rgba(56, 161, 105, 0.3); }
        .btn-complete::before { background: linear-gradient(135deg, #48BB78, var(--success)); }
        
        .btn-maps { background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; box-shadow: 0 10px 20px rgba(255, 123, 84, 0.3); }
        .btn-maps::before { background: linear-gradient(135deg, var(--secondary), var(--primary)); }
        
        .btn-call { background: white; color: var(--dark); border: 2px solid #E2E8F0; }
        .btn-call:hover { border-color: transparent; background: linear-gradient(135deg, #2D3748, #4A5568); box-shadow: 0 10px 20px rgba(45, 55, 72, 0.2); }

        .empty-state {
            background: white;
            border-radius: 24px;
            padding: 60px 20px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
            border: 2px dashed #E2E8F0;
        }

        .empty-icon {
            width: 100px;
            height: 100px;
            background: #F7FAFC;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: #A0AEC0;
            margin: 0 auto 20px;
            animation: bounce 3s infinite;
        }

        .empty-state h4 {
            color: var(--dark);
            font-weight: 800;
            font-size: 22px;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #718096;
            font-weight: 500;
        }

        @media (max-width: 991px) {
            .stat-card { padding: 20px; }
            .stat-info h3 { font-size: 24px; }
            .stat-icon { width: 50px; height: 50px; font-size: 22px; }
        }

        @media (max-width: 768px) {
            .top-nav .container { flex-wrap: wrap; gap: 15px; }
            .user-info { width: 100%; justify-content: space-between; order: 2; }
            .nav-actions { width: 100%; display: flex; gap: 10px; order: 3; }
            .nav-actions a { flex: 1; justify-content: center; }
            .order-header { flex-direction: column; align-items: flex-start; gap: 15px; }
        }

        /* Animations */
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
    </style>
</head>
<body>

<!-- Premium Glassmorphic Navbar -->
<nav class="top-nav animate__animated animate__fadeInDown">
    <div class="container">
        <a href="delivery_dashboard.php" class="logo">
            <i class="fas fa-shipping-fast"></i>
            Hummy Delivery
        </a>
        
        <div class="user-info">
            <div class="user-details d-none d-sm-block">
                <strong><?php echo $delivery_name; ?></strong>
                <span>مندوب توصيل ممتاز</span>
            </div>
            <div class="user-avatar">
                <i class="fas fa-user-ninja"></i>
            </div>
        </div>

        <div class="nav-actions">
            <a href="delivery_history.php" class="nav-btn btn-history">
                <i class="fas fa-clock"></i>
                سجل التوصيل
            </a>
            <a href="logout.php" class="nav-btn btn-logout">
                <i class="fas fa-sign-out-alt"></i>
                خروج
            </a>
        </div>
    </div>
</nav>

<!-- Statistics Cards -->
<div class="container stats-container">
    <div class="row g-4">
        <div class="col-xl-3 col-md-6 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
            <div class="stat-card pending">
                <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
                <div class="stat-info">
                    <h3><?php echo $pendingDeliveries; ?></h3>
                    <p>قيد الانتظار</p>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
            <div class="stat-card completed">
                <div class="stat-icon"><i class="fas fa-check-double"></i></div>
                <div class="stat-info">
                    <h3><?php echo $completedDeliveries; ?></h3>
                    <p>إجمالي المكتملة</p>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
            <div class="stat-card today">
                <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-info">
                    <h3><?php echo $todayDeliveries; ?></h3>
                    <p>توصيلات اليوم</p>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
            <div class="stat-card earnings">
                <div class="stat-icon"><i class="fas fa-wallet"></i></div>
                <div class="stat-info">
                    <h3><?php echo number_format($todayEarnings); ?></h3>
                    <p>أرباح اليوم (ريال)</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container animate__animated animate__fadeIn" style="animation-delay: 0.5s;">
    <!-- MY ACTIVE ORDERS (Assigned to me) -->
    <h3 class="section-title">
        <i class="fas fa-motorcycle bg-primary-grad"></i>
        طلباتي الحالية (في الطريق)
    </h3>
    
    <?php if ($my_active_orders->num_rows > 0): ?>
        <?php while ($order = $my_active_orders->fetch_assoc()): ?>
            <div class="order-card my-active-order">
                <div class="order-header">
                    <div class="order-id"><i class="fas fa-hashtag text-primary"></i> <?php echo $order['order_id']; ?></div>
                    <span class="order-badge badge-in-progress">
                        <i class="fas fa-spinner fa-spin"></i> <?php echo $order['order_status']; ?>
                    </span>
                </div>
                
                <div class="order-grid">
                    <div class="detail-item">
                        <div class="detail-icon"><i class="fas fa-user-circle"></i></div>
                        <div class="detail-text">
                            <span>اسم العميل</span>
                            <strong><?php echo $order['firstName'] . ' ' . $order['lastName']; ?></strong>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-icon"><i class="fas fa-phone-alt"></i></div>
                        <div class="detail-text">
                            <span>رقم التواصل</span>
                            <strong><a href="tel:<?php echo $order['phone']; ?>" style="text-decoration:none; color:inherit;"><?php echo $order['phone']; ?></a></strong>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="detail-text">
                            <span>عنوان التوصيل</span>
                            <strong><?php echo $order['address']; ?></strong>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-icon"><i class="fas fa-money-bill-wave"></i></div>
                        <div class="detail-text">
                            <span>المبلغ المطلوب</span>
                            <strong class="text-success"><?php echo number_format($order['grand_total']); ?> ريال</strong>
                        </div>
                    </div>
                </div>
                
                <div class="order-actions mt-4 pt-3 border-top">
                    <button class="action-btn btn-complete" onclick="updateStatus(<?php echo $order['order_id']; ?>, 'Completed')">
                        <i class="fas fa-check-circle fs-5"></i>
                        تأكيد التوصيل والاستلام
                    </button>
                    <button class="action-btn btn-maps" onclick="openMaps('<?php echo addslashes($order['address']); ?>')">
                        <i class="fas fa-directions fs-5"></i>
                        توجيه الخرائط
                    </button>
                    <a href="tel:<?php echo $order['phone']; ?>" class="action-btn btn-call text-decoration-none">
                        <i class="fas fa-phone fs-5"></i>
                        اتصال بالعميل
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty-state mb-5">
            <div class="empty-icon"><i class="fas fa-route"></i></div>
            <h4>الطريق خالي!</h4>
            <p>لا يوجد لديك طلبات قيد التوصيل في الوقت الحالي.</p>
        </div>
    <?php endif; ?>

    <!-- AVAILABLE ORDERS -->
    <h3 class="section-title mt-5">
        <i class="fas fa-box-open bg-warning-grad"></i>
        طلبات جديدة متاحة
    </h3>
    
    <?php if ($available_orders->num_rows > 0): ?>
        <?php while ($order = $available_orders->fetch_assoc()): ?>
            <div class="order-card">
                <div class="order-header">
                    <div class="order-id"><i class="fas fa-hashtag text-warning"></i> <?php echo $order['order_id']; ?></div>
                    <span class="order-badge badge-pending">
                        <i class="fas fa-clock"></i> <?php echo $order['order_status']; ?>
                    </span>
                </div>
                
                <div class="order-grid">
                    <div class="detail-item">
                        <div class="detail-icon"><i class="fas fa-user-circle"></i></div>
                        <div class="detail-text">
                            <span>اسم العميل</span>
                            <strong><?php echo $order['firstName'] . ' ' . $order['lastName']; ?></strong>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="detail-text">
                            <span>عنوان التوصيل</span>
                            <strong><?php echo $order['address']; ?></strong>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-icon"><i class="fas fa-money-bill-wave"></i></div>
                        <div class="detail-text">
                            <span>المبلغ المطلوب</span>
                            <strong class="text-success"><?php echo number_format($order['grand_total']); ?> ريال</strong>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-icon"><i class="fas fa-calendar-alt"></i></div>
                        <div class="detail-text">
                            <span>تاريخ ووقت الطلب</span>
                            <strong><span dir="ltr"><?php echo date('Y-m-d h:i A', strtotime($order['order_date'])); ?></span></strong>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($order['note'])): ?>
                    <div class="order-note">
                        <i class="fas fa-sticky-note mt-1"></i>
                        <div>
                            <span>ملاحظة العميل:</span><br>
                            <strong><?php echo htmlspecialchars($order['note']); ?></strong>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="order-actions mt-3 pt-3 border-top">
                    <button class="action-btn btn-start" onclick="updateStatus(<?php echo $order['order_id']; ?>, 'On the way')">
                        <i class="fas fa-motorcycle fs-5"></i>
                        استلام الطلب والانطلاق
                    </button>
                    <button class="action-btn btn-maps" onclick="openMaps('<?php echo addslashes($order['address']); ?>')">
                        <i class="fas fa-map-marked-alt fs-5"></i>
                        معاينة الموقع والمسار
                    </button>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-check-circle text-success"></i></div>
            <h4>كل شيء تم إنجازه!</h4>
            <p>لا توجد طلبات جديدة متاحة للتوصيل في الوقت الحالي.</p>
        </div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../toast.js"></script>

<script>
function updateStatus(orderId, status) {
    if (confirm('هل أنت متأكد من تغيير حالة الطلب؟')) {
        $.ajax({
            url: 'update_delivery_status.php',
            type: 'POST',
            data: {
                order_id: orderId,
                status: status
            },
            dataType: 'json',
            success: function(response) {
                showSuccess(response.message);
                setTimeout(() => {
                    location.reload();
                }, 1500);
            },
            error: function() {
                showError('حدث خطأ يرجى المحاولة مرة أخرى');
            }
        });
    }
}

function openMaps(address) {
    var url = 'https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(address);
    window.open(url, '_blank');
}
</script>

</body>
</html>
