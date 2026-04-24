<?php
session_start();
if (!isset($_SESSION['adminloggedin'])) {
    header("Location: ../login.php");
    exit();
}

include 'db_connection.php'; // Make sure to include your database connection

$statusFilter = isset($_GET['statusFilter']) ? $_GET['statusFilter'] : '';
$searchOrderId = isset($_GET['searchOrderId']) ? $_GET['searchOrderId'] : '';

$query = "SELECT orders.order_id, orders.order_date, orders.firstName, orders.lastName, orders.phone, orders.grand_total, orders.order_status, orders.pmode, orders.cancel_reason, staff.firstName as delivery_fname, staff.lastName as delivery_lname FROM orders LEFT JOIN staff ON orders.delivery_id = staff.id";
$conditions = [];

if (!empty($statusFilter)) {
    $conditions[] = "order_status = '" . $conn->real_escape_string($statusFilter) . "'";
}

if (!empty($searchOrderId)) {
    $conditions[] = "order_id LIKE '%" . $conn->real_escape_string($searchOrderId) . "%'";
}

if (!empty($conditions)) {
    $query .= " WHERE " . implode(' AND ', $conditions);
}

$query .= " ORDER BY order_id DESC";

$result = $conn->query($query);

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
    <!--poppins-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="sidebar.css">
    <link rel="stylesheet" href="admin_orders.css">
    <style>
  .content{
    margin-bottom: 40px;
  }
</style>
</head>

<body>
  <div class="sidebar"dir="rtl" style="text-align: right;">
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
            <li><a href="index.php" ><i class="fas fa-chart-line"></i> ملخص</a></li>
            <li><a href="admin_menu.php"><i class="fas fa-utensils"></i> إدارة الاقسام</a></li>
            <li><a href="admin_orders.php" class="active"><i class="fas fa-shopping-cart"></i> الطلبات</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> المستخدمون</a></li>
            <li><a href="reviews.php"><i class="fas fa-star"></i> التقييمات</a></li>
            <li><a href="staffs.php" ><i class="fas fa-users"></i> الموظفون</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i>إعداد الملف الشخصي</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a></li>
        </ul>
    </div>
    <div class="content" dir="rtl" style="text-align: right;">
        <div class="header">
            <button id="toggleSidebar" class="toggle-button">
                <i class="fas fa-bars"></i>
            </button>
            <h2><i class="fas fa-shopping-cart"></i> الطلبات</h2>
        </div>

        <div class="actions">
            <div>
            <button id="refreshButton" onclick="refreshPage()" title="Refresh">
                <i class="fas fa-sync-alt"></i>
            </button>
           
            </div>
            
            <div class="filter-orders">
                <select id="statusFilter" name="statusFilter" onchange="filterByStatus()">
                    <option value="">جميع الطلبات</option>
                    <option value="Pending">قيد الانتظار</option>
                    <option value="On Process">عملية</option>
                    <option value="On Process">في الطريق </option>
                    <option value="Completed">مكتمل</option>
                    <option value="Cancelled">تم الإلغاء</option>
                </select>
                <input type="text" id="searchOrderId" placeholder="البحث برقم الطلب" oninput="searchByOrderId()">
            </div>
        </div>
        <?php
        // Display orders in a table
        echo "<table>
                <tr>
                    <th>رقم الطلب</th>
                    <th>اسم العميل</th>
                    <th>رقم الهاتف</th>
                    <th>المجموع</th>
                    <th>حالة الطلب</th>
                    <th>طريقة الدفع</th>
                    <th>مندوب التوصيل</th>
                    <th>سبب الإلغاء</th>
                    <th>الإجراء</th>
                </tr>";
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $statusClass = '';
                switch ($row['order_status']) {
                    case 'قيد الانتظار':
                        $statusClass = 'status-pending';
                        break;
                    case 'يعالج':
                        $statusClass = 'status-processing';
                        break;
                    case 'مكتمل':
                        $statusClass = 'status-completed';
                        break;
                    case 'تم الإلغاء':
                        $statusClass = 'status-cancelled';
                        break;
                    case 'في الطريق':
                        $statusClass = 'status-ontheway';
                        break;
                }
                $delivery_name = ($row['delivery_fname'] && $row['delivery_lname']) ? $row['delivery_fname'] . " " . $row['delivery_lname'] : '-';
                echo "<tr>
                    <td>" . $row['order_id'] . "</td>
                    <td>" . $row['firstName'] . " " . $row['lastName'] . "</td>
                    <td>" . $row['phone'] . "</td>
                    <td>" . 'YER ' . $row['grand_total'] . "</td>
                    <td><span class='status $statusClass'>" . $row['order_status'] . "</span></td>
                    <td>" . $row['pmode'] . "</td>
                    <td>" . $delivery_name . "</td>
                    <td>" . ($row['order_status'] == 'Cancelled' ? $row['cancel_reason'] : '-') . "</td>
                    <td><button id='viewbtn' onclick=\"viewDetails(" . $row['order_id'] . ")\">عرض التفاصيل</button></td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='8' style='text-align: center;'>لم يتم العثور على أي طلبات</td></tr>";
        }

        echo "</table>";

        $conn->close();
        ?>
    </div>

    <?php
    include_once ('footer.html');
    ?>
    <script src="sidebar.js"></script>
    <script>
                function viewDetails(orderId) {
            window.location.href = 'view_order.php?orderId=' + orderId;
        }
    const modal = document.querySelector('.modal');
    const buttons = document.querySelectorAll('.toggle-button');

    buttons.forEach(button => {
        button.addEventListener('click', () => {
            modal.classList.toggle('open');
        });
    });

    function filterByStatus() {
        var statusFilter = document.getElementById('statusFilter').value;
        var dateFilter = document.getElementById('dateFilter') ? document.getElementById('dateFilter').value : ''; // Optional date filter
        var searchOrderId = document.getElementById('searchOrderId').value.trim();
        window.location.href = 'admin_orders.php?statusFilter=' + encodeURIComponent(statusFilter) + '&dateFilter=' + encodeURIComponent(dateFilter) + '&searchOrderId=' + encodeURIComponent(searchOrderId);
    }

    function searchByOrderId() {
        filterByStatus(); // Call filterByStatus to update results based on search input
    }

    function refreshPage() {
        window.location.href = 'admin_orders.php'; // Reload the page
    }

    // Set the status filter select value based on the query parameter
    document.getElementById('statusFilter').value = "<?= isset($_GET['statusFilter']) ? $_GET['statusFilter'] : '' ?>";

    // Optional: Set the date filter value if you have a date filter
    if (document.getElementById('dateFilter')) {
        document.getElementById('dateFilter').value = "<?= isset($_GET['dateFilter']) ? $_GET['dateFilter'] : '' ?>";
    }

    // Set the search input value based on the query parameter
    document.getElementById('searchOrderId').value = "<?= isset($_GET['searchOrderId']) ? $_GET['searchOrderId'] : '' ?>";

    // Attach event listeners to filters
    document.getElementById('statusFilter').addEventListener('change', filterByStatus);
    if (document.getElementById('dateFilter')) {
        document.getElementById('dateFilter').addEventListener('change', filterByStatus);
    }
    document.getElementById('searchOrderId').addEventListener('input', searchByOrderId);
    document.getElementById('refreshButton').addEventListener('click', refreshPage);
</script>



</body>

</html>