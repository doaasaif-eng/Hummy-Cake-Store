<?php
session_start();
require 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['userloggedin']) || $_SESSION['userloggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Get the order_id from the URL parameter
$orderId = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

// Fetch order details
$stmt = $conn->prepare('SELECT * FROM orders WHERE order_id=?');
if ($stmt === false) {
    die('Failed to prepare order details statement: ' . $conn->error);
}
$stmt->bind_param('i', $orderId);
$stmt->execute();
$orderResult = $stmt->get_result();
$order = $orderResult->fetch_assoc();

if ($order === null) {
    die('Order not found.');
}

// Fetch order items
$stmt = $conn->prepare('SELECT * FROM order_items WHERE order_id=?');
if ($stmt === false) {
    die('Failed to prepare order items statement: ' . $conn->error);
}
$stmt->bind_param('i', $orderId);
$stmt->execute();
$orderItemsResult = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css' />
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css' />
    <!--Bootstrap CSS-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>تأكيد الطلب</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap');
        
        body {
            background: linear-gradient(135deg, #F8FAFC 0%, #EFF6FF 100%);
            padding-top: 130px;
            padding-bottom: 70px;
            font-family: 'Cairo', sans-serif;
            color: #2D3748;
        }

        #wrapper {
            display: flex;
            flex-direction: column;
            gap: 25px;
            align-items: center;
        }

        .card {
            background: rgba(255, 255, 255, 0.75) !important;
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 15px 40px rgba(0,0,0,0.05);
            border-radius: 30px;
            width: 500px;
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            opacity: 0;
            transform: translateY(20px);
            animation: slideUp 0.6s cubic-bezier(0.165, 0.84, 0.44, 1) forwards;
        }

        .card:nth-child(1) { animation-delay: 0.1s; }
        .card:nth-child(2) { animation-delay: 0.3s; }
        .card:nth-child(3) { animation-delay: 0.5s; padding: 30px; }

        @keyframes slideUp {
            to { opacity: 1; transform: translateY(0); }
        }

        .icon {
            width: 80px; height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #48BB78, #38A169);
            box-shadow: 0 10px 25px rgba(72, 187, 120, 0.3);
            display: flex; justify-content: center; align-items: center;
            font-size: 40px; color: white; margin-bottom: 20px;
        }

        .card h3 { font-weight: 900; color: #2D3748; margin-bottom: 10px; font-size: 2rem; }
        .card p { font-size: 1.2rem; color: #718096; font-weight: 500; }

        .card ul { width: 100%; list-style: none; padding: 0; margin: 0; }
        .card ul > li {
            display: flex; justify-content: space-between; align-items: flex-start;
            padding: 15px 0; border-bottom: 1px dashed rgba(0,0,0,0.1);
        }
        .card ul > li:last-child { border-bottom: none; }

        .card ul > li span { font-size: 1.1rem; color: #4A5568; font-weight: 600; }
        .card ul > li span:first-child { color: #FF7B54; font-weight: 800; min-width: 120px; }
        .card ul > li span:last-child { text-align: left; }
        .card ul > li ul li { border: none; padding: 5px 0; color: #2D3748; font-weight: 700; }

        .cta-row { display: flex; gap: 15px; width: 100%; }
        .cta-row a { flex: 1; text-decoration: none; }

        .cta-row button {
            width: 100%; padding: 15px; border: none; border-radius: 15px;
            font-weight: 800; font-size: 1.1rem; cursor: pointer; transition: 0.3s;
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }

        .cta-row button.primary { background: linear-gradient(135deg, #FF7B54, #FFB26B); color: white; box-shadow: 0 10px 20px rgba(255, 123, 84, 0.3); }
        .cta-row button.primary:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(255, 123, 84, 0.4); }

        .cta-row button.secondary { background: white; color: #FF7B54; border: 2px solid #FF7B54; }
        .cta-row button.secondary:hover { background: #FF7B54; color: white; }

        @media (max-width: 600px) {
            .card { width: 90%; padding: 25px; }
            .card ul > li { flex-direction: column; gap: 5px; }
            .card ul > li span:last-child { text-align: right; }
            .cta-row { flex-direction: column; }
        }
    </style>
</head>

<body>
    <?php include('nav-logged.php'); ?>
    <div class="title d-flex justify-content-center align-items-center" dir="rtl" style="text-align: right;">
        <div id="wrapper">
            <div class="card">
                <div class="icon"><i class="fas fa-check"></i></div>
                <h3>شكراً لطلبك!</h3>
                <p>تم تقديم طلبك بنجاح.</p>
            </div>
            <div class="card">
                <ul>
                    <li>
                        <span><strong>رقم الطلب:</strong></span>
                        <span>#<?= htmlspecialchars($order['order_id'] ?? 'N/A') ?></span>
                    </li>
                    <li>
                        <span><strong>البريد الالكتروني:</strong></span>
                        <span><?= htmlspecialchars($order['email'] ?? 'N/A') ?></span>
                    </li>
                    <li>
                        <span><strong>طريقة الدفع:</strong></span>
                        <span><?= htmlspecialchars($order['pmode'] ?? 'N/A') ?></span>
                    </li>
                    <li>
                        <span><strong>عناصر الطلب:</strong></span>
                        <span>
                            <ul>
                                <?php while ($item = $orderItemsResult->fetch_assoc()) : ?>
                                    <li><?= htmlspecialchars($item['itemName']) ?> - <?= htmlspecialchars($item['quantity']) ?></li>
                                <?php endwhile; ?>
                            </ul>
                        </span>
                    </li>
                    <li>
                        <span><strong>المجموع :</strong></span>
                        <span>YER <?= number_format($order['grand_total']) ?></span>
                    </li>
                    <li>
                        <span><strong>العنوان:</strong></span>
                        <span><?= htmlspecialchars($order['address'] ?? 'N/A') ?></span>
                    </li>
                </ul>
            </div>
            <div class="card">
                <div class="cta-row">
                    <a href="menu1.php">
                        <button class="secondary">
                            العودة إلى القائمة
                        </button>
                    </a>
                    <a href="orders.php">
                        <button class="primary">
                            تتبع الطلب
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php
include_once ('footer.html');
?>

<script>
    $(document).ready(function() {
      console.log('Page is ready. Calling load_cart_item_number.');
      load_cart_item_number();

      function load_cart_item_number() {
        $.ajax({
          url: 'action.php',
          method: 'get',
          data: {
            cartItem: "cart_item"
          },
          success: function(response) {
            $("#cart-item").html(response);
          }
        });
      }
    });
  </script>
</body>

</html>