<?php

include 'db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['userloggedin']) || !$_SESSION['userloggedin']) {
  header('Location: login.php');
  exit;
}

// Fetch user profile image
$useremail = isset($_SESSION['email']) ? $_SESSION['email'] : '';
$sql = "SELECT profile_image FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);

if (empty($useremail)) {
  die("لم يتم العثور على بريد المستخدم الإلكتروني في الجلسة.");
}

// Function to retrieve admin information
function get_UserInfo($email)
{
  global $conn;
  $stmt = $conn->prepare("SELECT  profile_image FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->bind_result($profile_image);
  $stmt->fetch();
  $stmt->close();
  return [
    'profile_image' => $profile_image ?: 'default.jpg'
  ];
}

$userinfo = get_UserInfo($useremail);
// Close the statement and connection

?>

<!DOCTYPE html>
<html lang="ar">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!--Bootstrap CSS-->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
  <!--Lexend font for navbar-->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!--Icon-->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
  <!-- Chewy Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Chewy&display=swap" rel="stylesheet">
  <title>شريط التنقل</title>
  <style>
    :root {
      --primary: #fb4a36;
      --secondary: #FFB26B;
      --dark: #2c1a1a;
      --glass-bg: rgba(255, 255, 255, 0.98);
      --glass-border: rgba(251, 74, 54, 0.1);
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      background: linear-gradient(135deg, #F8FAFC 0%, #EFF6FF 100%);
      font-family: "Poppins", 'Cairo', sans-serif;
    }

    a { text-decoration: none; transition: 0.3s; }

    /* Premium Solid Navbar */
    .navbar {
      background: var(--glass-bg);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border-bottom: 1px solid var(--glass-border);
      box-shadow: 0 5px 20px rgba(251, 74, 54, 0.05);
      padding: 15px 0;
      transition: all 0.3s ease;
    }

    .nav-container { margin: 0 3%; display: flex; align-items: center; width: 100%; }

    .logo {
      color: var(--primary) !important;
      font-family: "Chewy", system-ui;
      font-size: 32px;
      font-weight: 700;
      background: linear-gradient(135deg, #fb4a36, #FFB26B);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      transition: 0.3s;
    }
    
    .logo:hover { transform: scale(1.05); }

    .nav-link {
      color: var(--dark) !important;
      font-weight: 600;
      font-family: "Lexend", "Cairo", sans-serif;
      transition: 0.3s all ease;
      position: relative;
    }

    .nav-link:hover, .nav-link.active, .navbar .active {
      color: var(--primary) !important;
      transform: translateY(-2px);
    }

    /* Hover Dropdown */
    .dropdown-menu {
      border: none !important;
      border-radius: 18px !important;
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08) !important;
      padding: 12px;
      transform: translateY(15px);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      text-align: right;
      display: block;
      opacity: 0;
      visibility: hidden;
      margin-top: 10px;
    }

    .nav-item.dropdown:hover .dropdown-menu {
      transform: translateY(0);
      opacity: 1;
      visibility: visible;
    }

    .dropdown-item {
      color: var(--dark);
      border-radius: 12px;
      font-weight: 600;
      padding: 10px 15px;
      transition: 0.3s;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .dropdown-item:hover {
      background: rgba(255, 123, 84, 0.1) !important;
      color: var(--primary) !important;
      padding-right: 20px;
    }

    /* Profile specific */
    .nav-profile {
      width: 45px; 
      height: 45px; 
      border-radius: 50%; 
      object-fit: cover; 
      border: 3px solid white; 
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); 
      transition: 0.3s;
    }
    .nav-profile:hover {
      transform: scale(1.1);
      box-shadow: 0 6px 20px rgba(255, 123, 84, 0.3);
    }

    /* Icons */
    .cart { font-size: 22px; position: relative; margin-right: 15px; color: var(--dark) !important; }
    .cart:hover { color: var(--primary) !important; transform: scale(1.1); }
    .badge-danger { position: absolute; top: -5px; right: -8px; background: var(--primary); color: white; border-radius: 50%; padding: 3px 6px; font-size: 10px; font-weight: bold; border: 2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }

    /* Offcanvas */
    .offcanvas-body { background: #F8FAFC; }
    .offcanvas-header { border-bottom: 1px solid rgba(0,0,0,0.05); background: white; }
    .offcanvas-title { color: var(--primary); font-weight: 800; font-family: 'Cairo'; }
    .btn-close-red { filter: brightness(0) saturate(100%) invert(35%) sepia(99%) saturate(3015%) hue-rotate(346deg) brightness(97%) contrast(105%); }
    .navbar-toggler { border: none; }
    .navbar-toggler:focus { box-shadow: none; }
    .navbar-toggler-icon { filter: invert(0.5); }
  </style>
</head>

<body>
  <?php
  // Get the current page name
  $current_page = basename($_SERVER['PHP_SELF']);
  ?>

  <!-- Navbar -->
  <div>
    <nav class="navbar navbar-expand-md fixed-top" dir="rtl">
      <div class="container-fluid nav-container">
        <a class="navbar-brand me-auto logo" href="index.php">Hummy Cake</a>
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
          <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasNavbarLabel">
              Hummy Cake
            </h5>
            <button type="button" class="btn-close btn-close-red" aria-label="Close" id="closeOffcanvas"></button>
          </div>
          <div class="offcanvas-body text-center">
            <ul class="navbar-nav justify-content-center flex-grow-1 pe-3">
              <li class="nav-item">
                <a class="nav-link mx-lg-2 <?php echo $current_page == 'index.php' ? 'active' : ''; ?>" aria-current="page" href="index.php">الرئيسية</a>
              </li>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle mx-lg-2 <?php echo ($current_page == 'menu1.php') ? 'active' : ''; ?>" 
       href="menu1.php" 
       id="navbarDropdownMenuLink" 
       role="button" 
       data-toggle="dropdown" 
       aria-haspopup="true" 
       aria-expanded="false">
        قائمة الأطباق 
    </a>
    
    <ul class="dropdown-menu shadow-sm" aria-labelledby="navbarDropdownMenuLink">
        <li>
            <a class="dropdown-item" href="menu1.php">
                <i class="fas fa-th-large ml-2"></i> عرض الكل
            </a>
        </li>
        <div class="dropdown-divider"></div>
        <li>
            <a class="dropdown-item" href="category.php?cat=<?= urlencode('قسم الجاتو') ?>">
                <i class="fas fa-birthday-cake ml-2"></i> قسم الجاتو
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="category.php?cat=<?= urlencode('قسم العصائر') ?>">
                <i class="fas fa-glass-cheers ml-2"></i> قسم العصائر
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="category.php?cat=<?= urlencode('قسم المعجنات') ?>">
                <i class="fas fa-bread-slice ml-2"></i> قسم المعجنات
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="category.php?cat=<?= urlencode('قسم المقبلات') ?>">
                <i class="fas fa-cheese ml-2"></i> قسم المقبلات
            </a>
        </li>
    </ul>
</li>
              <li class="nav-item">
                <a class="nav-link mx-lg-2 <?php echo $current_page == 'index.php#Reservation' ? 'active' : ''; ?>" href="orders.php#Reservation">الطلبات</a>
              </li>
              <li class="nav-item">
                <a class="nav-link mx-lg-2 <?php echo $current_page == 'index.php#About-Us' ? 'active' : ''; ?>" href="index.php#About-Us">معلومات عنا</a>
              </li>
              <li class="nav-item">
                <a class="nav-link mx-lg-2 <?php echo $current_page == 'index.php#review' ? 'active' : ''; ?>" href="index.php#review">التقييمات</a>
              </li>
            </ul>
          </div>
        </div>
        <a class="nav-link cart <?php echo $current_page == 'cart.php' ? 'active' : ''; ?>" href="cart.php"><i class="fas fa-shopping-cart"></i>
          <span id="cart-item" class="badge badge-danger"></span></a>
        
        <a href="delivery/delivery_login.php" class="nav-link" style="color: #fb4a36; font-size: 20px; margin-left: 10px;" title="تسجيل دخول رجل التوصيل">
          <i class="fas fa-motorcycle"></i>
        </a>
        
        <!-- Profile Icon with Dropdown Menu -->
        <li class="nav-item dropdown ms-3" style="list-style: none; ">
          <a href="#" class="dropdown-toggle" id="profileDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <img src="uploads/<?php echo htmlspecialchars($userinfo['profile_image']); ?>" alt="صورة الشخصية" class="nav-profile">
          </a>
          <ul class="dropdown-menu" aria-labelledby="profileDropdown" style="margin-left: -50px;">
            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-circle dropdown-icon"></i> بيانات الشخصية</a></li>
            <li><a class="dropdown-item" href="orders.php"><i class="fas fa-box dropdown-icon"></i> طلبات</a></li>
            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt dropdown-icon"></i> تسجيل الخروج</a></li>
          </ul>
        </li>


        <button class="navbar-toggler" type="button" aria-label="Toggle navigation" id="toggleOffcanvas">
          <span class="navbar-toggler-icon" style="color: #f9f6e8"></span>
        </button>
      </div>
    </nav>
  </div>





  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js"></script>
  <!--Bootstrap JS-->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>



  <script>
    const closeOffcanvasBtn = document.getElementById("closeOffcanvas");
    const toggleOffcanvasBtn = document.getElementById("toggleOffcanvas");
    const offcanvasNavbar = new bootstrap.Offcanvas(
      document.getElementById("offcanvasNavbar")
    );

    closeOffcanvasBtn.addEventListener("click", function() {
      offcanvasNavbar.hide();
    });

    toggleOffcanvasBtn.addEventListener("click", function() {
      offcanvasNavbar.show();
    });
  </script>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const sections = document.querySelectorAll("section");
      const navLinks = document.querySelectorAll(".navbar-nav .nav-link");
      const currentPage = window.location.pathname.split("/").pop(); // Get the current page name

      function removeActiveClasses() {
        navLinks.forEach(link => {
          link.classList.remove("active");
        });
      }

      function addActiveClassOnScroll() {
        let currentSection = "Home"; // Default to Home if on index.php

        // Check if the current page is index.php
        if (currentPage === "index.php") {
          sections.forEach(section => {
            const sectionTop = section.offsetTop;
            if (pageYOffset >= sectionTop - 60) {
              currentSection = section.getAttribute("id");
            }
          });

          removeActiveClasses();

          if (currentSection === "Reservation" || currentSection === "About-Us" || currentSection === "review") {
            const activeLink = document.querySelector(`.navbar-nav a[href*="${currentSection}"]`);
            if (activeLink) {
              activeLink.classList.add("active");
            }
          } else {
            // Default to highlighting Home when on index.php
            const homeLink = document.querySelector(`.navbar-nav a[href="index.php"]`);
            if (homeLink) {
              homeLink.classList.add("active");
            }
          }
        } else {
          // Highlight the current page if it's not index.php
          const activeLink = document.querySelector(`.navbar-nav a[href="${currentPage}"]`);
          if (activeLink) {
            removeActiveClasses();
            activeLink.classList.add("active");
          }
        }
      }

      window.addEventListener("scroll", addActiveClassOnScroll);
      addActiveClassOnScroll(); // Call it initially to set the correct tab on page load
    });
  </script>



</body>

</html>