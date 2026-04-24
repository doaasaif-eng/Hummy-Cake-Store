<?php
session_start();
if (!isset($_SESSION['adminloggedin'])) {
  header("Location: ../login.php");
  exit();
}

include 'db_connection.php';
?>
<?php
include 'sidebar.php';
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>إدارة التقييمات المسؤول</title>

  <!--poppins-->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="admin_reservation.css">
  <link rel="stylesheet" href="sidebar.css">
  <link rel="stylesheet" href="admin_review.css">
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
      <li><a href="index.php"><i class="fas fa-chart-line" style="margin-right: 10px;"></i> ملخص</a></li>
      <li><a href="admin_menu.php" ><i class="fas fa-utensils" style="margin-right: 10px;"></i> إدارة الاطباق</a></li>
      <li><a href="admin_orders.php"><i class="fas fa-shopping-cart" style="margin-right: 10px;"></i> الطلبات</a></li>
      <li><a href="users.php" class="active"><i class="fas fa-users" style="margin-right: 10px;"></i> المستخدمون</a></li>
      <li><a href="reviews.php"><i class="fas fa-star" style="margin-right: 10px;"></i> التقييمات</a></li>
      <li><a href="staffs.php" ><i class="fas fa-users" style="margin-right: 10px;"></i> الموظفون</a></li>
      <li><a href="profile.php"><i class="fas fa-user" style="margin-right: 10px;"></i> إعداد الملف الشخصي</a></li>
      <li style="margin-right: 10px;"><a href="logout.php"><i class="fas fa-sign-out-alt" style="margin-right: 10px;"></i> تسجيل الخروج</a></li>
    </ul>
  </div>
  
  <div class="content" dir="rtl" style="text-align: right;">
    <div class="header">
      <button id="toggleSidebar" class="toggle-button">
        <i class="fas fa-bars"></i>
      </button>
      <h2><i class="fas fa-star"></i> التقييمات</h2>
    </div>

    <div class="actions">
      <select id="statusFilter" name="statusFilter" onchange="filterByStatus()">
        <option value="">الكل</option>
        <option value="pending">قيد الانتظار</option>
        <option value="approved">موافقة</option>
        <option value="rejected">مرفوض</option>
      </select>
    </div>

    <div class="table">
      <table id="reviewTable">
        <thead>
          <tr>
            <th>رقم الطلب</th>
            <th>البريد الالكتروني</th>
            <th>التقييم</th>
            <th>تصنيف</th>
            <th>حالة</th>
            <th>الردود</th>
            <th>الإجراءات</th>
          </tr>
        </thead>
        <tbody>
          <?php
// Include the database connection
include 'db_connection.php';

// Query to fetch all reviews
$sql = "SELECT * FROM reviews";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
  // If there are rows, display them
  while ($row = mysqli_fetch_assoc($result)) {
    // Convert rating to stars
    $ratingStars = str_repeat('&#9733;', $row['rating']) . str_repeat('&#9734;', 5 - $row['rating']);

    echo "<tr>
                        <td>{$row['order_id']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['review_text']}</td>
                        <td class='rating-stars'>{$ratingStars}</td>
                        <td>
                         <select id='status-{$row['order_id']}' onchange='updateStatus({$row['order_id']}, this.value)' class='status-select'>
                         <option value='pending' " . ($row['status'] == 'pending' ? 'selected' : '') . ">قيد الانتظار</option>
                         <option value='approved' " . ($row['status'] == 'approved' ? 'selected' : '') . ">موافقة</option>
                         <option value='rejected' " . ($row['status'] == 'rejected' ? 'selected' : '') . ">مرفوض</option>
                         </select>
                        </td>

                        <td>{$row['response']}</td>
                        <td>
                            <button id='editbtn' onclick='openEditReviewModal(this)' data-id='{$row['order_id']}' data-email='{$row['email']}' data-review_text='{$row['review_text']}' data-rating='{$row['rating']}' data-response='{$row['response']}'><i class='fas fa-edit'></i></button>
                            <button id='deletebtn' onclick=\"deleteReview('{$row['order_id']}', '{$row['email']}')\"><i class='fas fa-trash'></i></button>
                        </td>
                      </tr>";
  }
}
else {
  // If no rows, display the "No Reviews" message
  echo "<tr><td colspan='6' style='text-align: center;'>No Reviews</td></tr>";
}

// Close the database connection
mysqli_close($conn);
?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal for editing review -->
  <div id="editReviewModal" class="modal" dir="rtl" style="text-align: right;">
    <div class="modal-overlay" dir="rtl" style="text-align: right;"></div>
    <div class="modal-container">
      <form id="editReviewForm" method="POST" action="edit_review.php">
        <div class="modal-header">
          <h2>تعديل التقييم</h2>
          <span class="close-icon" onclick="closeEditReviewModal()">&times;</span>
        </div>
        <div class="modal-content">
          <div class="input-group">
            <input type="number" name="order_id" id="editOrder_id" class="input" readonly>
            <label for="editOrder_id" class="label">رقم الطلب</label>
          </div>
        </div>
        <div class="modal-content">
          <div class="input-group">
            <input type="email" name="email" id="editEmail" class="input" readonly>
            <label for="editEmail" class="label">البريد الالكتروني</label>
          </div>
        </div>
        <div class="modal-content">
          <div class="input-group">
            <input type="text" name="review_text" id="editReview_text" class="input" readonly>
            <label for="editReview_text" class="label">التقييم</label>
          </div>
        </div>
        <div class="modal-content">
          <div class="input-group">
            <input type="text" name="rating" id="editRating" class="input" readonly>
            <label for="editRating" class="label">تصنيف</label>
          </div>
        </div>
        <div class="modal-content">
          <div class="input-group">
            <input type="text" name="response" id="editResponse" class="input" required>
            <label for="editResponse" class="label">الردود</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="button" onclick="closeEditReviewModal()">الغاء</button>
          <button type="submit" class="button">حفظ</button>
        </div>
      </form>
    </div>
  </div>

  <?php
include_once('footer.html');
?>
  <script>
   function updateStatus(order_id, status) {
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "update_review_status.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4 && xhr.status === 200) {
      // Check if the response indicates success
      if (xhr.responseText.trim() === "تم تحديث الحالة بنجاح") {
        // Optionally, display a success message
        alert("تم تحديث الحالة بنجاح");
      } else {
        // Display an error message
        alert("حدث خطأ أثناء تحديث الحالة: " + xhr.responseText);
      }
    }
  };
  xhr.send("order_id=" + encodeURIComponent(order_id) + "&status=" + encodeURIComponent(status));
}



    function deleteReview(orderId, email) {
      // Confirm and handle review deletion
      if (confirm('هل أنت متأكد من رغبتك في حذف هذا التقييم؟')) {
        // Send delete request to server
        fetch('delete_review.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              orderId: orderId,
              email: email
            })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              alert('تم حذف المراجعة بنجاح');
              location.reload(); // Reload the page to see the updated list
            } else {
              alert('حدث خطأ أثناء حذف التقييم');
            }
          });
      }
    }

    function openEditReviewModal(button) {
      // Get user data from data attributes
      const order_id = button.getAttribute('data-id');
      const email = button.getAttribute('data-email');
      const review_text = button.getAttribute('data-review_text');
      const rating = button.getAttribute('data-rating');
      const response = button.getAttribute('data-response');

      // Set the values in the editReviewForm
      document.getElementById('editOrder_id').value = order_id;
      document.getElementById('editEmail').value = email;
      document.getElementById('editReview_text').value = review_text;
      document.getElementById('editRating').value = rating;
      document.getElementById('editResponse').value = response;

      // Open the edit review modal
      document.getElementById('editReviewModal').classList.add('open');
    }

    function closeEditReviewModal() {
      document.getElementById('editReviewModal').classList.remove('open');
    }

    function filterByStatus() {
    // Get the selected status from the dropdown
    const status = document.getElementById('statusFilter').value;

    // Create an XMLHttpRequest object
    var xhr = new XMLHttpRequest();

    // Set up the request
    xhr.open('POST', 'fetch_review_status.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    // Define what happens on successful data submission
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Update the table with the filtered results
            document.querySelector('index.php#reviewTable tbody').innerHTML = xhr.responseText;
        }
    };

    // Send the request with the selected status
    xhr.send('status=' + encodeURIComponent(status));
}

  </script>
</body>

</html>