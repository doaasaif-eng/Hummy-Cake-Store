<?php
session_start();
if (!isset($_SESSION['adminloggedin'])) {
  header("Location: ../login.php");
  exit();
}

include 'db_connection.php';
$orderId = isset($_GET['orderId']) ? $_GET['orderId'] : '';

if ($orderId) {
  $orderQuery = "SELECT * FROM orders WHERE order_id = ?";
  $stmt = $conn->prepare($orderQuery);
  $stmt->bind_param('i', $orderId);
  $stmt->execute();
  $orderResult = $stmt->get_result();
  $order = $orderResult->fetch_assoc();

  $itemsQuery = "SELECT * FROM order_items WHERE order_id = ?";
  $itemsQuery = "SELECT itemName, quantity, price, total_price, image FROM order_items WHERE order_id = ?";

  $stmt = $conn->prepare($itemsQuery);
  $stmt->bind_param('i', $orderId);
  $stmt->execute();
  $itemsResult = $stmt->get_result();
} else {
  echo "رقم الطلب غير صالح.";
  exit();
}
$paymentMode = $order['pmode'] ?? 'takeaway'; // Default to 'takeaway' if not set

// Determine the delivery fee based on the payment mode
$deliveryFee = ($paymentMode === 'takeaway') ? 0 : 130;
?>
<?php
include 'sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>إدارة الطلبات</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
  <link rel="stylesheet" href="sidebar.css">
  <link rel="stylesheet" href="admin_orders.css">
  <link rel="stylesheet" href="view_order.css">

</head>

<body>
  <div class="sidebar">
    <button class="close-sidebar" id="closeSidebar">&times;</button>

    <!-- Profile Section -->
    <div class="profile-section">
      <img src="../uploads/<?php echo htmlspecialchars($admin_info['profile_image']); ?>" alt="Profile Picture">
      <div class="info">
        <h3>مرحبًا بعودتك!</h3>
        <p><?php echo htmlspecialchars($admin_info['firstName']) . ' ' . htmlspecialchars($admin_info['lastName']); ?></p>
      </div>
    </div>

    <!-- Navigation Items -->
    <ul>
      <li><a href="index.php"><i class="fas fa-chart-line"></i> ملخص</a></li>
      <li><a href="admin_menu.php"><i class="fas fa-utensils"></i>إدارة الاقسام</a></li>
      <li><a href="admin_orders.php" class="active"><i class="fas fa-shopping-cart"></i> الطلبات</a></li>
      <li><a href="users.php"><i class="fas fa-users"></i> المستخدمون</a></li>
      <li><a href="reviews.php"><i class="fas fa-star"></i> التقييمات</a></li>
      <li><a href="staffs.php"><i class="fas fa-users"></i> الموظفون</a></li>
      <li><a href="profile.php"><i class="fas fa-user"></i> إعداد الملف الشخصي</a></li>
      <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a></li>
    </ul>
  </div>
  <div class="content" dir="rtl" style="text-align: right;">
    <div class="header">
      <div class="col">
        <button id="toggleSidebar" class="toggle-button">
          <i class="fas fa-bars"></i>
        </button>
        <h2><i class="fas fa-shopping-cart"></i>#<?php echo $order['order_id']; ?>تفاصيل الطلب</h2>
      </div>
      <div class="col d-flex justify-content-end">
        <a href="admin_orders.php" class="button"><i class="fas fa-arrow-left"></i>&nbsp; الطلبات</a>
      </div>
    </div>
    <div class="details">
      <div class="order-details">
        <div class="order-items">
          <h4 class="mt-2">عناصر الطلب</h4>
          <hr>
          <ul class="list-group">
            <?php while ($item = $itemsResult->fetch_assoc()) : ?>
              <li class=" d-flex justify-content-between  mb-3">
                <div class="d-flex align-items-start">
                  <?php
                  // Check if 'image' key exists and is not empty
                  if (!empty($item['image'])) {
                    echo '<img src="../uploads/' . htmlspecialchars($item['image']) . '" alt="Item Image" style="width: 70px; height: 70px; object-fit: cover;">';
                  } else {
                    echo '<span>لا توجد صورة متاحة</span>';
                  }
                  ?>
                  <?php echo $item['itemName']; ?>
                </div>
                <div>
                  <div class="d-flex flex-row justify-content-between align-items-start quantity-price">
                    <div>
                      YER <?php echo $item['price']; ?> x <?php echo $item['quantity']; ?>
                    </div>
                  </div>
                  <div class="d-flex flex-row justify-content-end align-items-end">
                    <span class="badge rounded-pill text-light p-2 mt-2" style="background-color: #fb4a36; ">YER<?php echo $item['total_price']; ?></span>
                  </div>
                </div>
              </li>
            <?php endwhile; ?>
          </ul>
        </div>
        <div class="order-summary">
          <h4 class="mt-2">سعر الطلب</h4>
          <hr>
          <div class="summary-details">
            <p><strong>المجموع:</strong></p>
            <p> YER <?php echo $order['sub_total']; ?></p>
          </div>

          <div class="summary-details">
            <p><strong>السعر:</strong></p>
            <p>YER <?= number_format($deliveryFee, 2) ?></p>
          </div>
          <div class="summary-details">
            <p><strong>المجموع:</strong></p>
            <p> YER <?php echo $order['grand_total']; ?></p>
          </div>
          <div class="summary-details">
            <p><strong>طريقة الدفع:</strong></p>
            <p><?php echo $order['pmode']; ?></p>
          </div>
          <div class="summary-details">
            <p style="width: 60%;"><strong>حالة الدفع:</strong></p>
            <select class="form-select" id="paymentStatus" name="payment_status">
              <option value="Pending" <?php if ($order['payment_status'] == 'Pending') echo 'selected'; ?>>قيد الانتظار</option>
              <option value="Successful" <?php if ($order['payment_status'] == 'Successful') echo 'selected'; ?>>تم</option>
              <option value="Rejected" <?php if ($order['payment_status'] == 'Rejected') echo 'selected'; ?>>ملغي</option>
            </select>
          </div>
          <div class="summary-details">
            <p><strong>سبب الإلغاء:</strong></p>
            <p><?php echo $order['cancel_reason']; ?></p>
          </div>
          <hr>
          <form method="post" action="update_order_status.php" onsubmit="return validateForm()">
            <div class="status-container">
              <label for="orderStatus" class="form-label"><strong>حالة الطلب</strong></label>
              <select class="form-select" id="orderStatus" name="order_status">
                <option value="Pending" <?php if ($order['order_status'] == 'Pending') echo 'selected'; ?>>قيد الانتظار</option>
                <option value="Processing" <?php if ($order['order_status'] == 'Processing') echo 'selected'; ?>>يعالج</option>
                <option value="Completed" <?php if ($order['order_status'] == 'Completed') echo 'selected'; ?>>مكتمل</option>
                <option value="Cancelled" <?php if ($order['order_status'] == 'Cancelled') echo 'selected'; ?>>تم الإلغاء</option>
                <option value="On the way" <?php if ($order['order_status'] == 'On the way') echo 'selected'; ?>>في الطريق</option>
              </select>
            </div>
            <div class="mb-3" id="cancelReasonContainer" style="display: none;">
              <label for="cancelReason" class="form-label">سبب الإلغاء</label>
              <textarea class="form-control" id="cancelReason" name="cancel_reason"></textarea>
            </div>
            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
            <button type="submit" id="statusbtn">تحديث الحالة</button>
          </form>
        </div>
      </div>
      <div class="customer mb-4">
        <h4 class="mt-2">بيانات العميل</h4>
        <hr>
        <div class="customer-details">
          <div class="summary-details">
            <p><strong>الإسم:</strong></p>
            <p><?php echo $order['firstName'] . ' ' . $order['lastName']; ?></p>
          </div>
          <div class="summary-details">
            <p><strong>بريد إلكتروني:</strong></p>
            <p><?php echo $order['email']; ?></p>
          </div>
          <div class="summary-details">
            <p><strong>رقم الهاتف:</strong></p>
            <p><?php echo $order['phone']; ?></p>
          </div>
          <div class="summary-details">
            <p><strong>العنوان:</strong></p>
            <p><?php echo $order['address']; ?></p>
          </div>
          <div class="summary-details">
            <p><strong>ملاحظة بخصوص الطلب:</strong></p>
            <p><?php echo $order['note']; ?></p>
          </div>
        </div>
      </div>
    </div>

  </div>
  <?php
  include_once('footer.html');
  ?>
  <script src="sidebar.js"></script>
  <script>
    document.getElementById('paymentStatus').addEventListener('change', function() {
      var paymentStatus = this.value;
      var orderId = <?php echo $order['order_id']; ?>; // Get order ID from PHP

      // Create an AJAX request
      var xhr = new XMLHttpRequest();
      xhr.open('POST', 'update_payment_status.php', true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

      // Handle the response
      xhr.onload = function() {
        if (xhr.status === 200) {
          // Optionally, show a success message or handle errors
          alert('تم تحديث حالة الدفع بنجاح');
        } else {
          console.error('حدث خطأ أثناء تحديث حالة الدفع');
        }
      };

      // Send the request with order ID and payment status
      xhr.send('order_id=' + encodeURIComponent(orderId) + '&payment_status=' + encodeURIComponent(paymentStatus));
    });


    document.getElementById('orderStatus').addEventListener('change', function() {
      const cancelReasonContainer = document.getElementById('cancelReasonContainer');
      if (this.value === 'Cancelled') {
        cancelReasonContainer.style.display = 'block';
      } else {
        cancelReasonContainer.style.display = 'none';
      }
    });

    function validateForm() {
      const orderStatus = document.getElementById('orderStatus').value;
      if (orderStatus === 'Cancelled') {
        const cancelReason = document.getElementById('cancelReason').value;
        if (cancelReason.trim() === '') {
          alert('يرجى ذكر سبب الإلغاء.');
          return false;
        }
      }
      return true;
    }
  </script>
</body>

</html>