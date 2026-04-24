<?php
session_start();
require 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['userloggedin']) || $_SESSION['userloggedin'] !== true) {
  header('location:login.php');
  exit;
}

// Get the email from the session
$email = $_SESSION['email'];

// Fetch user data
$stmt = $conn->prepare('SELECT * FROM users WHERE email=?');
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Retrieve selected items from POST request
$selectedItems = json_decode($_POST['selected_items'], true);

// Fetch cart items from the database
$itemDetails = [];
foreach ($selectedItems as $item) {
  $stmt = $conn->prepare('SELECT * FROM cart WHERE id=? AND email=?');
  $stmt->bind_param('is', $item['id'], $email);
  $stmt->execute();
  $result = $stmt->get_result();
  $itemDetails[] = $result->fetch_assoc();
}

// Calculate subtotal
$subtotal = 0;
foreach ($itemDetails as $item) {
  $itemPrice = $item['price'];
  $itemQuantity = $item['quantity'];
  $subtotal += $itemPrice * $itemQuantity;
}

// Delivery fee will be calculated in JavaScript based on selected area
$deliveryFee = 0;
$total = $subtotal + $deliveryFee;

// Areas in Sana'a governorate with distances from Taiz Street (in km)
$areas = [
  ['id' => 'pickup', 'name' => 'استلام من المتجر', 'distance' => 0],
  ['id' => 'taiz_street', 'name' => 'شارع تعز', 'distance' => 0.5],
  ['id' => 'tahrir', 'name' => 'التحرير', 'distance' => 1],
  ['id' => 'sabeen', 'name' => 'السبعين', 'distance' => 2],
  ['id' => 'safiah', 'name' => 'حي الصافية', 'distance' => 3],
  ['id' => 'shamailan', 'name' => 'حي شميلة', 'distance' => 2.5],
  ['id' => 'azariq', 'name' => 'حي العزري', 'distance' => 4],
  ['id' => 'zilfi', 'name' => 'حي الزيلعي', 'distance' => 3.5],
  ['id' => 'kuwait', 'name' => 'الكويت', 'distance' => 4.5],
  ['id' => 'hadda', 'name' => 'حي حدة', 'distance' => 5],
  ['id' => 'assabi', 'name' => 'الثقة', 'distance' => 2],
  ['id' => 'maqdis', 'name' => 'المقدسي', 'distance' => 3],
  ['id' => 'hadiqa', 'name' => 'حديقة', 'distance' => 4],
  ['id' => 'ghubai', 'name' => 'الغبي', 'distance' => 5],
  ['id' => 'maqam', 'name' => 'المقام', 'distance' => 4],
  ['id' => 'rahaba', 'name' => 'الرحبة', 'distance' => 6],
  ['id' => 'hadhan', 'name' => 'حاشد', 'distance' => 3],
  ['id' => 'matari', 'name' => 'المطري', 'distance' => 3.5],
  ['id' => 'khames', 'name' => 'خمس', 'distance' => 7],
  ['id' => 'bain', 'name' => 'بينون', 'distance' => 8],
  ['id' => 'sanaa_street', 'name' => 'شارع صنعاء', 'distance' => 2],
  ['id' => 'new_sanaa', 'name' => 'صنعاء الجديدة', 'distance' => 5],
  ['id' => 'airport', 'name' => 'المنطقة الصناعية', 'distance' => 6],
  ['id' => 'sittin', 'name' => 'شارع الستين', 'distance' => 4],
  ['id' => 'wadi', 'name' => 'وادي قريش', 'distance' => 5],
  ['id' => 'dhahban', 'name' => 'ذهبان', 'distance' => 7],
  ['id' => 'bait', 'name' => 'بيت بوعمر', 'distance' => 6],
  ['id' => 'jar', 'name' => 'الجراف', 'distance' => 8],
  ['id' => 'noqom', 'name' => 'النقوم', 'distance' => 9],
  ['id' => 'khalf', 'name' => 'خلف', 'distance' => 4],
];

?>

<!DOCTYPE html>
<html lang="ar">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css' />
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css' />
  <!-- Bootstrap CSS -->
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="order_review.css">
  <title>إتمام الطلب</title>
  <style>
    .delivery-options {
      display: flex;
      gap: 15px;
      margin-bottom: 20px;
    }
    
    .delivery-option {
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 15px 20px;
      border: 2px solid rgba(0,0,0,0.1);
      background: rgba(255,255,255,0.8);
      border-radius: 15px;
      cursor: pointer;
      transition: all 0.3s ease;
      flex: 1;
      font-size: 1.1rem;
      font-weight: 700;
      color: #4A5568;
    }
    
    .delivery-option:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
      border-color: rgba(255, 123, 84, 0.4);
    }
    
    .delivery-option.selected {
      background: linear-gradient(135deg, #FF7B54, #FFB26B);
      border-color: transparent;
      color: white;
      box-shadow: 0 10px 20px rgba(255, 123, 84, 0.3);
    }
    
    .delivery-option input {
      display: none;
    }
    
    .delivery-option i {
      margin-left: 10px;
      font-size: 1.3rem;
    }
    
    .area-select {
      margin-bottom: 20px;
    }
    
    .distance-info {
      padding: 15px;
      background: rgba(255, 123, 84, 0.05);
      border-radius: 15px;
      border: 1px dashed rgba(255, 123, 84, 0.5);
      margin-bottom: 20px;
      display: none;
      font-size: 1.1rem;
      color: #2D3748;
    }
    
    .distance-info.show {
      display: block;
    }
  </style>
</head>

<body>
  <?php include('nav-logged.php'); ?>
  <div class="title mt-2">
    <h3>اهلا <?php echo $user['firstName'] . " " . $user['lastName']; ?>أكمل طلبك!</h3>
  </div>
  <div class=" main mt-4" style="direction: rtl; text-align: right;">
    <div class="order-fee">

      <h4>تفاصيل الطلب</h4>
      <hr>
      <form action="process_order.php" method="post" id="orderForm">
        <input type="hidden" name="total" value="<?= $total ?>">
        <input type="hidden" name="subtotal" value="<?= $subtotal ?>">
        <input type="hidden" name="order_id" value="<?= $orderId ?>">
        <input type="hidden" name="selected_items" value='<?= json_encode($selectedItems) ?>'>
        <input type="hidden" name="payment_mode" value="<?= htmlspecialchars($_POST['payment_mode']) ?>">
        <input type="hidden" name="delivery_fee" id="deliveryFeeInput" value="0">
        <input type="hidden" name="delivery_type" id="deliveryTypeInput" value="pickup">
        <input type="hidden" name="area_distance" id="areaDistanceInput" value="0">
        
        <div class="form-group row">
          <div class="col">
            <label for="firstName">الإسم الاول:</label>
            <input type="text" class="form-control" id="firstName" name="firstName" required>
          </div>
          <div class="col">
            <label for="lastName">الإسم الاخير:</label>
            <input type="text" class="form-control" id="lastName" name="lastName" required>
          </div>
        </div>
        <div class="form-group row">
          <div class="col">
            <label for="contact">رقم الهاتف:</label>
            <input type="text" class="form-control" id="contact" name="contact" required>
          </div>
          <div class="col">
            <label for="email">البريد الالكتروني:</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email) ?>" readonly style="direction: ltr;">
          </div>
        </div>
        <div class="form-group">
          <label for="order_note">ملاحظة بخصوص الطلب:</label>
          <textarea class="form-control" id="order_note" name="order_note" rows="3"></textarea>
        </div>
        
        <!-- Delivery Type Selection -->
        <div class="form-group">
          <label>طريقة استلام الطلب:</label>
          <div class="delivery-options">
            <label class="delivery-option selected" id="pickupOption">
              <input type="radio" name="delivery_type" value="pickup" checked onchange="updateDeliveryType(this.value)">
              <i class="fas fa-store"></i>
              <span>استلام من المتجر</span>
            </label>
            <label class="delivery-option" id="deliveryOption">
              <input type="radio" name="delivery_type" value="delivery" onchange="updateDeliveryType(this.value)">
              <i class="fas fa-motorcycle"></i>
              <span>توصيل للمنزل</span>
            </label>
          </div>
        </div>
        
        <!-- Area Selection (for delivery) -->
        <div class="form-group area-select" id="areaSelectGroup" style="display: none;">
          <label for="area">اختر منطقتك:</label>
          <select class="form-control dropdown-premium" id="area" name="area" onchange="calculateDeliveryFee()" style="padding: 10px; border: 2px solid #eee; border-radius: 8px;">
            <option value="">اختر المنطقة...</option>
            <?php foreach ($areas as $area): ?>
              <?php if ($area['id'] !== 'pickup'): ?>
                <option value="<?= $area['id'] ?>" data-distance="<?= $area['distance'] ?>">
                  <?= $area['name'] ?> (<?= $area['distance'] ?> كم)
                </option>
              <?php endif; ?>
            <?php endforeach; ?>
          </select>
        </div>
        
        <!-- Distance Info -->
        <div class="distance-info" id="distanceInfo">
          <strong>المسافة من شارع تعز: </strong>
          <span id="distanceValue">0</span> كم
          <br>
          <strong>سعر التوصيل (200 ريال لكل كم): </strong>
          <span id="deliveryCost">0</span> ريال يمني
        </div>
        
        <!-- Address Field -->
        <div class="form-group" id="addressGroup" style="display: none;">
          <label for="address">العنوان بالتفصيل:</label>
          <textarea class="form-control" id="address" name="address" rows="3" placeholder="أدخل العنوان بالتفصيل..."></textarea>
        </div>

    </div>




    <div class="order-summary">
      <h4>تفاصيل الطلب</h4>
      <hr>
      <div class="order-items mb-2">
        <?php foreach ($itemDetails as $item) : ?>
          <div class="order-item d-flex align-items-center">
            <?php if (!empty($item['image'])) : ?>
              <img src="uploads/<?= htmlspecialchars($item['image']) ?>" alt="Item Image" class="ms-1">
            <?php else : ?>
              <span>لا توجد صورة متاحة</span>
            <?php endif; ?>
            <div class="ms-1 row d-flex justify-content-between w-100">
              <div class="col d-flex flex-column justify-content-center ">
                <div class="d-flex flex-row mb-1"><strong><?= htmlspecialchars($item['itemName']) ?></strong></div>
                <div class="d-flex flex-row ">العدد: <?= htmlspecialchars($item['quantity']) ?></div>
              </div>
              <div class="col d-flex flex-column justify-content-center">
                <div class="d-flex flex-row justify-content-end align-items-center mt-2" style="font-weight: 800; color: #2D3748;"> YER <?= htmlspecialchars($item['price'], 0) ?> x <?= htmlspecialchars($item['quantity']) ?></div>
                <div class="d-flex flex-row justify-content-end align-items-start mb-2">
                  <span class="badge rounded-pill text-light p-2 mt-2 item-total-price" style="background: linear-gradient(135deg, #FF7B54, #FFB26B);">YER <?= $item['total_price'] ?></span>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <h4 class="mt-1 ">سعر الطلب</h4>
      <hr>
      <div class="summary-details">
        <div class="fee-details">
          <div><strong>المجموع :</strong></div>
          <div>YER <?= number_format($subtotal) ?></div>
        </div>
        <div class="fee-details">
          <div><strong>طريقة الدفع:</strong></div>
          <div><?= htmlspecialchars($_POST['payment_mode']) ?></div>
        </div>
        <div class="fee-details">
          <div><strong>سعر التوصيل</strong></div>
          <div>YER <span id="deliveryFeeDisplay">0</span></div>
        </div>
        <div class="fee-details">
          <div><strong>الإجمالي:</strong></div>
          <div>YER <span id="totalDisplay"><?= number_format($total) ?></span></div>
        </div>
      </div>
      <hr>
      <?php
      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $payment_mode = $_POST['payment_mode'] ?? '';

        if ($payment_mode == 'Card') {
          echo '<button type="submit" class="Button">
            دفع
           <svg viewBox="0 0 576 512" class="svgIcon"><path d="M512 80c8.8 0 16 7.2 16 16v32H48V96c0-8.8 7.2-16 16-16H512zm16 144V416c0 8.8-7.2 16-16 16H64c-8.8 0-16-7.2-16-16V224H528zM64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H512c35.3 0 64-28.7 64-64V96c0-35.3-28.7-64-64-64H64zm56 304c-13.3 0-24 10.7-24 24s10.7 24 24 24h48c13.3 0 24-10.7 24-24s-10.7-24-24-24H120zm128 0c-13.3 0-24 10.7-24 24s10.7 24 24 24H360c13.3 0 24-10.7 24-24s-10.7-24-24-24H248z"></path></svg>
           </button>';
        } else {
          echo '<button type="submit" class="order-btn ">الطلب</button>';
        }
      }
      ?>

      </form>
    </div>


  </div>

  <?php
include_once ('footer.html');
?>

  <!-- Bootstrap JS -->
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap-bundle.min.js"></script>
  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
  <script src='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.min.js'></script>
  

  <script>
    const PER_KM_RATE = 200; // Price per kilometer in Yemeni Rials
    let currentDeliveryFee = 0;
    let subtotal = <?= $subtotal ?>;

    // Function to update delivery type
    function updateDeliveryType(type) {
      const pickupOption = document.getElementById('pickupOption');
      const deliveryOption = document.getElementById('deliveryOption');
      const areaSelectGroup = document.getElementById('areaSelectGroup');
      const addressGroup = document.getElementById('addressGroup');
      const distanceInfo = document.getElementById('distanceInfo');
      
      if (type === 'pickup') {
        pickupOption.classList.add('selected');
        deliveryOption.classList.remove('selected');
        areaSelectGroup.style.display = 'none';
        addressGroup.style.display = 'none';
        distanceInfo.classList.remove('show');
        
        // Set delivery fee to 0
        currentDeliveryFee = 0;
        document.getElementById('deliveryFeeInput').value = 0;
        document.getElementById('deliveryTypeInput').value = 'pickup';
        document.getElementById('areaDistanceInput').value = 0;
        
        // Update display
        document.getElementById('deliveryFeeDisplay').textContent = '0';
        document.getElementById('totalDisplay').textContent = subtotal.toLocaleString();
        
      } else {
        deliveryOption.classList.add('selected');
        pickupOption.classList.remove('selected');
        areaSelectGroup.style.display = 'block';
        addressGroup.style.display = 'block';
        
        document.getElementById('deliveryTypeInput').value = 'delivery';
      }
    }

    // Function to calculate delivery fee based on distance
    function calculateDeliveryFee() {
      const areaSelect = document.getElementById('area');
      const selectedOption = areaSelect.options[areaSelect.selectedIndex];
      const distance = parseFloat(selectedOption.dataset.distance) || 0;
      
      // Calculate delivery fee: distance * rate per km
      currentDeliveryFee = Math.round(distance * PER_KM_RATE);
      
      // Update hidden inputs
      document.getElementById('deliveryFeeInput').value = currentDeliveryFee;
      document.getElementById('areaDistanceInput').value = distance;
      
      // Update distance info display
      const distanceInfo = document.getElementById('distanceInfo');
      if (distance > 0) {
        distanceInfo.classList.add('show');
        document.getElementById('distanceValue').textContent = distance;
        document.getElementById('deliveryCost').textContent = currentDeliveryFee.toLocaleString();
      } else {
        distanceInfo.classList.remove('show');
      }
      
      // Update order summary
      document.getElementById('deliveryFeeDisplay').textContent = currentDeliveryFee.toLocaleString();
      const total = subtotal + currentDeliveryFee;
      document.getElementById('totalDisplay').textContent = total.toLocaleString();
      
      // Update the total in the form
      document.querySelector('input[name="total"]').value = total;
    }

    // Make radio buttons work with custom styling
    document.querySelectorAll('.delivery-option').forEach(option => {
      option.addEventListener('click', function() {
        const radio = this.querySelector('input[type="radio"]');
        radio.checked = true;
        updateDeliveryType(radio.value);
      });
    });
  </script>

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
