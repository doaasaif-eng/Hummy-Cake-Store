<?php
session_start();
include 'db_connection.php';

$popularItems = [];
if ($result = $conn->query("SELECT itemName, image, price FROM menuitem WHERE is_popular = 1")) {
  while ($row = $result->fetch_assoc()) { $popularItems[] = $row; }
  $result->close();
}

$cats = [
  "قسم الجاتو"    => ["icon" => "fa-birthday-cake",  "img" => "images/wedding-cake.png"],
  "قسم العصائر"   => ["icon" => "fa-glass-cheers",   "img" => "images/ice-cream-juice-.png"],
  "قسم المعجنات"  => ["icon" => "fa-bread-slice",    "img" => "images/croissant.png"],
  "قسم المقبلات"  => ["icon" => "fa-cheese",         "img" => "images/Knafa.jpg"],
];
$conn->close();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hummy Cake | الصفحة الرئيسية</title>
  <meta name="description" content="متجر حلويات Hummy Cake — أشهى الحلويات، الجاتو، والمعجنات بكل حب.">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Cairo Font -->
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
  <!-- Owl Carousel -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css" rel="stylesheet">
  <!-- AOS -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <!-- Our Stylesheet -->
  <link rel="stylesheet" href="index.css">
</head>
<body>

<!-- Loading Screen -->
<div id="loading-screen">
  <div class="loader-container">
    <div class="loader-logo">🍰</div>
    <div class="loader-bar"><div class="progress"></div></div>
    <p>جاري تحضير أشهى الأطباق...</p>
  </div>
</div>

<!-- Navbar -->
<?php
if (isset($_SESSION['userloggedin']) && $_SESSION['userloggedin']) {
  include 'nav-logged.php';
} else {
  include 'navbar.php';
}
?>

<!-- ====================================================
     1. HERO CAROUSEL
     ==================================================== -->
<div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
  </div>
  <div class="carousel-inner">

    <!-- Slide 1 -->
    <div class="carousel-item active">
      <div class="hero-slide" style="background-image:url('images/main-bg.png')">
        <div class="hero-overlay"></div>
        <div class="hero-body" data-aos="zoom-in" data-aos-duration="1000">
          <h1>مرحباً بكم في <span style="color:#FFB26B;">Hummy Cake</span></h1>
          <p>حيث تلتقي النكهات الدافئة بمتعة الحلويات الفاخرة المخبوزة بكل حب وعناية.</p>
          <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="menu1.php" class="btn-hero-filled"><i class="fas fa-shopping-cart"></i> أطلب الآن</a>
            <a href="#categories" class="btn-hero-ghost">استكشف القائمة</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Slide 2 -->
    <div class="carousel-item">
      <div class="hero-slide" style="background-image:url('images/wedding-cake.png')">
        <div class="hero-overlay"></div>
        <div class="hero-body">
          <h1>جاتوهات المناسبات</h1>
          <p>نصنع كعك الزفاف والحفلات بأرقى التفاصيل لتخليد أجمل لحظاتكم.</p>
          <a href="category.php?cat=<?= urlencode('قسم الجاتو') ?>" class="btn-hero-filled"><i class="fas fa-cake-candles"></i> تسوق الآن</a>
        </div>
      </div>
    </div>

    <!-- Slide 3 -->
    <div class="carousel-item">
      <div class="hero-slide" style="background-image:url('images/Knafa.jpg')">
        <div class="hero-overlay"></div>
        <div class="hero-body">
          <h1>طعم لا يُقاوم</h1>
          <p>انغمس في تشكيلتنا الواسعة من الحلويات الشرقية والغربية الطازجة يومياً.</p>
          <a href="menu1.php" class="btn-hero-filled"><i class="fas fa-star"></i> شاهد العروض</a>
        </div>
      </div>
    </div>

  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" style="background:rgba(0,0,0,0.5);border-radius:50%;padding:22px;"></span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon" style="background:rgba(0,0,0,0.5);border-radius:50%;padding:22px;"></span>
  </button>
</div>

<!-- ====================================================
     2. STATS BAND
     ==================================================== -->
<div class="band">
  <div class="container">
    <div class="row row-cols-2 row-cols-md-4 g-3 text-center">
      <div class="col"><div class="stat-item"><h2>١٠٠+</h2><p>طبق متنوع</p></div></div>
      <div class="col"><div class="stat-item"><h2>٥٠٠٠+</h2><p>عميل سعيد</p></div></div>
      <div class="col"><div class="stat-item"><h2>١٠٠%</h2><p>مكونات طازجة</p></div></div>
      <div class="col"><div class="stat-item"><h2>٣+</h2><p>سنوات خبرة</p></div></div>
    </div>
  </div>
</div>

<!-- ====================================================
     3. CATEGORIES
     ==================================================== -->
<section id="categories" class="categories-section">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <div class="section-title">قائمة <span>الأطباق</span></div>
      <p class="section-sub">تصفح أقسامنا المتنوعة والغنية بألذ الخيارات</p>
    </div>

    <!-- Filter Tabs -->
    <div class="d-flex justify-content-center flex-wrap mb-5" data-aos="fade-up">
      <a href="menu1.php" class="tag-item active"><i class="fas fa-th-large"></i> الكل</a>
      <?php foreach ($cats as $name => $data): ?>
        <a href="category.php?cat=<?= urlencode($name) ?>" class="tag-item">
          <i class="fas <?= $data['icon'] ?>"></i> <?= $name ?>
        </a>
      <?php endforeach; ?>
    </div>

    <!-- Category Grid -->
    <div class="cat-grid">
      <?php $delay = 0; foreach ($cats as $name => $data): $delay += 100; ?>
      <div class="cat-card" style="cursor:pointer" onclick="location.href='category.php?cat=<?= urlencode($name) ?>'"
           data-aos="fade-up" data-aos-delay="<?= $delay ?>">
        <img src="<?= $data['img'] ?>" alt="<?= $name ?>">
        <div class="cat-card-caption">
          <h3><?= $name ?></h3>
          <span>استكشف الآن ←</span>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ====================================================
     4. POPULAR ITEMS
     ==================================================== -->
<section class="popular-section">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <div class="section-title">أفضل <span>اختياراتنا</span></div>
      <p class="section-sub">أطباق مميزة نالت استحسان وإعجاب الجميع</p>
    </div>

    <div id="popularCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="7000">
      <div class="carousel-inner">
        <?php
        if (!empty($popularItems)) {
          $chunks = array_chunk($popularItems, 3);
          $active = true;
          foreach ($chunks as $chunk):
        ?>
        <div class="carousel-item <?= $active ? 'active' : '' ?>">
          <div class="row g-4 justify-content-center">
            <?php foreach ($chunk as $item):
              $img = !empty($item['image']) ? 'uploads/'.$item['image'] : 'images/default-food.png';
            ?>
            <div class="col-lg-4 col-md-6">
              <div class="product-card">
                <div class="product-img-box">
                  <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($item['itemName']) ?>">
                </div>
                <div class="product-name"><?= htmlspecialchars($item['itemName']) ?></div>
                <div class="product-price"><?= number_format($item['price']) ?> <small style="font-size:.8rem;color:var(--muted)">ريال</small></div>
                <button class="add-to-cart-btn" onclick="handleAddToCart()">
                  <i class="fas fa-cart-plus"></i> أضف للسلة
                </button>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php $active = false; endforeach; } ?>
      </div>
      <!-- Navigation Arrows -->
      <div class="text-center mt-4">
        <button class="pop-nav-btn" type="button" data-bs-target="#popularCarousel" data-bs-slide="prev">
          <i class="fas fa-arrow-right"></i>
        </button>
        <button class="pop-nav-btn" type="button" data-bs-target="#popularCarousel" data-bs-slide="next">
          <i class="fas fa-arrow-left"></i>
        </button>
      </div>
    </div>

  </div>
</section>

<!-- ====================================================
     5. WHY CHOOSE US
     ==================================================== -->
<section id="why-choose-us" class="why-section">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6" data-aos="fade-left">
        <div class="section-title">لماذا <span>تختارنا؟</span></div>
        <p class="section-sub">نضمن لكم تجربة ضيافة لا تُنسى من خلال جودة المكونات وسرعة الخدمة.</p>

        <div class="feature-item">
          <div class="feature-icon-wrap">
            <img src="icons/delivery-man.png" alt="توصيل سريع">
          </div>
          <div class="feature-text">
            <h4>توصيل سريع وموثوق</h4>
            <p>استمتع بتوصيل سريع إلى باب منزلك في أسرع وقت ممكن.</p>
          </div>
        </div>

        <div class="feature-item">
          <div class="feature-icon-wrap">
            <img src="icons/vegetables.png" alt="مكونات طازجة">
          </div>
          <div class="feature-text">
            <h4>مكونات طازجة 100%</h4>
            <p>نستخدم فقط أفضل المكونات الطازجة لصنع حلوياتنا الفاخرة.</p>
          </div>
        </div>

        <div class="feature-item">
          <div class="feature-icon-wrap">
            <img src="icons/waiter (1).png" alt="خدمة ودودة">
          </div>
          <div class="feature-text">
            <h4>خدمة عملاء ودودة</h4>
            <p>طاقمنا دائماً متوفر لخدمتك بابتسامة وترحاب دافئ.</p>
          </div>
        </div>
      </div>

      <div class="col-lg-6" data-aos="fade-right">
        <div class="why-image">
          <img src="images/Why-Us.png" alt="لماذا تختارنا">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ====================================================
     6. ABOUT US
     ==================================================== -->
<section id="About-Us" class="about-section">
  <div class="container">
    <div class="about-box" data-aos="fade-up">
      <div class="row align-items-center g-5">
        <div class="col-lg-6">
          <div class="section-title">من <span>نحن</span></div>
          <p class="section-sub">نُصنع وجبات ولحظات لا تُنسى!</p>
          <p>في <strong>Hummy Cake</strong>، نحن نعشق الحلويات ونؤمن بأنها ليست مجرد طعام، بل هي صانعة للفرح واللحظات التي تظل محفورة في الذاكرة.</p>
          <p>نهتم بتقديمها بأسلوب أنيق وبسيط يليق بذوقكم الرفيع، مع عناية فائقة بكل تفصيلة صغيرة لتضيف طعماً أجمل للتجربة.</p>
          <p>هدفنا أن نكون دائماً قريبين منكم، نشارككم لحظاتكم السعيدة بكل حب واهتمام.</p>
          <a href="menu1.php" class="btn-brand"><i class="fas fa-heart"></i> اطلب الآن</a>
        </div>
        <div class="col-lg-6">
          <img src="images/icon.png" alt="Hummy Cake" class="about-img">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ====================================================
     7. TESTIMONIALS
     ==================================================== -->
<section id="review" class="testi-section">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <div class="section-title">آراء <span>عملائنا</span></div>
      <p class="section-sub">ماذا يقول عشاق ومتذوقي Hummy Cake؟</p>
    </div>
    <div class="clients-carousel owl-carousel">
      <?php
      include 'db_connection.php';
      $q = "SELECT r.review_text, r.rating, u.firstName, u.lastName, u.profile_image
            FROM reviews r JOIN users u ON r.email = u.email
            WHERE r.status = 'approved' ORDER BY r.review_id DESC LIMIT 6";
      $res = mysqli_query($conn, $q);
      if ($res && mysqli_num_rows($res) > 0):
        while ($row = mysqli_fetch_assoc($res)):
          $img  = !empty($row['profile_image']) ? $row['profile_image'] : 'default.jpg';
          $name = htmlspecialchars($row['firstName'].' '.$row['lastName']);
          $text = htmlspecialchars($row['review_text']);
      ?>
      <div class="review-card">
        <div class="quote-mark"><i class="fas fa-quote-right"></i></div>
        <img src="uploads/<?= $img ?>" alt="<?= $name ?>" class="review-img">
        <div class="stars">
          <?php for($i=1;$i<=5;$i++) echo $i<=$row['rating'] ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>'; ?>
        </div>
        <p class="review-text">"<?= $text ?>"</p>
        <div class="reviewer-name"><?= $name ?></div>
      </div>
      <?php endwhile; else: ?>
        <p class="text-center">لا توجد تقييمات حالياً.</p>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- ====================================================
     8. FOOTER
     ==================================================== -->
<footer>
  <div class="container">
    <div class="row g-4" style="text-align:right;">
      <div class="col-lg-4 col-md-6">
        <div class="footer-logo">🍰 Hummy Cake</div>
        <p class="footer-desc">نحن نقدم أشهى وأرقى أنواع الحلويات المخبوزة بشغف وحب. انضم إلينا لتذوق الفرق في كل قضمة.</p>
        <div class="social-row">
          <a href="#" class="social-btn"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="social-btn"><i class="fab fa-instagram"></i></a>
          <a href="#" class="social-btn"><i class="fab fa-twitter"></i></a>
          <a href="#" class="social-btn"><i class="fab fa-snapchat-ghost"></i></a>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="ft-title">روابط سريعة</div>
        <ul class="ft-links">
          <li><a href="index.php">الرئيسية</a></li>
          <li><a href="menu1.php">قائمة الأطباق</a></li>
          <li><a href="cart.php">السلة</a></li>
          <li><a href="#review">تقييمات العملاء</a></li>
          <li><a href="#About-Us">من نحن</a></li>
        </ul>
      </div>
      <div class="col-lg-4 col-md-12">
        <div class="ft-title">تواصل معنا</div>
        <div class="ft-contact-item"><i class="fas fa-map-marker-alt"></i> اليمن - صنعاء</div>
        <div class="ft-contact-item"><i class="fas fa-envelope"></i> info@hummycake.com</div>
        <div class="ft-contact-item"><i class="fas fa-phone-alt"></i> +967 000 000 000</div>
      </div>
    </div>
    <div class="footer-divider">
      &copy; 2026 جميع الحقوق محفوظة لدى <strong>Hummy Cake</strong>.
    </div>
  </div>
</footer>

<!-- Toast Notification -->
<div class="custom-toast" id="myToast">
  <i class="fas fa-exclamation-circle"></i>
  <span>يجب <a href="login.php" class="toast-link">تسجيل الدخول</a> أولاً لإضافة منتجات للسلة.</span>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  // Init AOS
  AOS.init({ duration: 800, once: true });

  // Load cart count
  $(function() {
    $.get('action.php', { cartItem: 'cart_item' }, function(r) { $('#cart-item').html(r); });

    // Testimonials Owl Carousel
    $('.clients-carousel').owlCarousel({
      loop: true, nav: false, autoplay: true,
      autoplayTimeout: 5500, margin: 25, rtl: true,
      responsive: { 0:{items:1}, 768:{items:2}, 1200:{items:3} }
    });
  });

  // Handle add to cart
  function handleAddToCart() {
    var loggedIn = <?php echo isset($_SESSION['userloggedin']) ? 'true' : 'false'; ?>;
    if (!loggedIn) {
      var t = document.getElementById('myToast');
      t.classList.add('show');
      setTimeout(function(){ t.classList.remove('show'); }, 4000);
    }
  }

  // Hide loader
  window.addEventListener('load', function() {
    setTimeout(function() {
      var l = document.getElementById('loading-screen');
      l.style.opacity = '0';
      l.style.visibility = 'hidden';
    }, 900);
  });
</script>
</body>
</html>