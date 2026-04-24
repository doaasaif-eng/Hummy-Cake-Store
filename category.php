<?php
session_start();
include 'db_connection.php';

if (!isset($_GET['cat'])) {
    header("Location: menu1.php");
    exit;
}

$category = $_GET['cat'];

// مصفوفة لربط الأقسام بالأيقونات الخاصة بها
$cats_with_icons = [
    "قسم الجاتو" => "fa-birthday-cake",
    "قسم العصائر" => "fa-glass-cheers",
    "قسم المعجنات" => "fa-bread-slice",
    "قسم المقبلات" => "fa-cheese"
];

// تحديد أيقونة القسم الحالي للعنوان الرئيسي
$current_icon = $cats_with_icons[$category] ?? "fa-utensils";
?>

<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($category) ?></title>

<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css' />
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css' />

<style>
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap');

    body {
        background: linear-gradient(135deg, #F8FAFC 0%, #EFF6FF 100%);
        font-family: 'Cairo', sans-serif;
        color: #2D3748;
        margin: 0;
        padding: 0;
        min-height: 100vh;
    }

    .main-wrapper {
        padding-top: 100px; 
        width: 85%; 
        margin: 0 auto;
    }

    .section-title-container {
        text-align: center;
        margin-bottom: 40px;
    }
    .section-title-container h1 {
        font-weight: 900;
        color: #2c1a1a;
        font-size: 2.5rem;
    }
    .section-title-container h1 span {
        color: #fb4a36;
    }
    .title-icon {
        color: #fb4a36;
        margin-left: 15px;
        font-size: 2.2rem;
        vertical-align: middle;
    }
    .title-line {
        width: 60px;
        height: 4px;
        background: #fb4a36;
        margin: 10px auto;
        border-radius: 50px;
    }

    /* أزرار الأقسام العلويّة */
    .tag-item {
        display: inline-flex;
        align-items: center;
        padding: 10px 20px;
        background-color: #f9f5f0;
        border: 1px solid rgba(44, 26, 26, 0.05);
        border-radius: 12px;
        text-decoration: none !important;
        color: #2c1a1a !important; 
        font-weight: 700;
        transition: 0.3s;
        font-size: 0.95rem;
        margin: 5px;
    }
    .tag-item i {
        margin-left: 10px;
        color: #fb4a36;
    }
    .tag-item:hover, .tag-item.active {
        background-color: #fb4a36 !important;
        color: #fff !important;
    }
    .tag-item:hover i, .tag-item.active i {
        color: #fff;
    }

    /* --- تنسيق الكرت الزجاجي الفاخر --- */
    .product-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.6);
        border-radius: 25px;
        padding: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04); 
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
    }
    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(255, 123, 84, 0.15);
        background: rgba(255, 255, 255, 0.9);
        border-color: rgba(255, 123, 84, 0.3);
    }

    .img-wrapper {
        width: 100%;
        height: 180px; 
        border-radius: 20px;
        overflow: hidden;
        background: transparent;
        margin-bottom: 10px;
    }
    .img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 5px;
        transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        filter: drop-shadow(0 10px 15px rgba(0,0,0,0.1));
    }
    .product-card:hover .img-wrapper img {
        transform: scale(1.12) rotate(3deg);
    }
    
    .card-content {
        padding: 10px 5px 5px 5px;
        text-align: center;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .product-name {
        font-size: 1.2rem;
        font-weight: 800;
        color: #2D3748;
        margin-bottom: 5px;
    }
    .product-price {
        font-size: 1.4rem;
        color: #FF7B54;
        font-weight: 900;
        margin-bottom: 15px;
    }
    
    .add-to-cart-btn {
        background: linear-gradient(135deg, #FF7B54, #FFB26B);
        color: #fff;
        border: none;
        width: 100%;
        padding: 12px;
        border-radius: 15px;
        font-weight: 800;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 8px 20px rgba(255, 123, 84, 0.25);
    }
    .add-to-cart-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 25px rgba(255, 123, 84, 0.4);
    }

    #toast {
        position: fixed;
        bottom: 20px;
        left: 20px;
        z-index: 10000;
        background: #2c1a1a;
        color: #fff;
        padding: 12px 25px;
        border-radius: 10px;
        display: none;
    }

    #loading-screen {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: #fdf2e9; /* نفس لون خلفية الموقع */
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 99999; /* لضمان ظهورها فوق كل شيء */
    transition: opacity 0.5s ease, visibility 0.5s;
}

.loader-container {
    text-align: center;
}

.loader-logo {
    font-size: 50px;
    margin-bottom: 20px;
    animation: bounce 1s infinite alternate;
}

.loader-bar {
    width: 200px;
    height: 6px;
    background-color: #ddd;
    border-radius: 10px;
    overflow: hidden;
    margin: 0 auto 15px;
}

.progress {
    width: 0%;
    height: 100%;
    background-color: #fb4a36; /* اللون البرتقالي المحمر */
    border-radius: 10px;
    animation: loading-bar 2s ease-in-out infinite;
}

#loading-screen p {
    font-family: 'Cairo', sans-serif;
    color: #2c1a1a;
    font-weight: 700;
    font-size: 1.1rem;
}

/* حركات الأنميشن */
@keyframes bounce {
    from { transform: translateY(0); }
    to { transform: translateY(-20px); }
}

@keyframes loading-bar {
    0% { width: 0%; }
    50% { width: 70%; }
    100% { width: 100%; }
}

/* كلاس للاختفاء */
.loader-hidden {
    opacity: 0;
    visibility: hidden;
}

</style>
</head>

<body>

<div id="loading-screen">
    <div class="loader-container">
        <div class="loader-logo">😋</div> 
        <div class="loader-bar">
            <div class="progress"></div>
        </div>
        <p>جاري تحضير أشهى الأطباق...</p>
    </div>
</div>

<?php
if (isset($_SESSION['userloggedin']) && $_SESSION['userloggedin']) {
    include 'nav-logged.php';
} else {
    include 'navbar.php';
}
?>

<div class="main-wrapper">
    <section class="category-page py-4" dir="rtl">
        <div id="message"></div>

        <div class="section-title-container">
            <h1>
                <i class="fas <?= $current_icon ?> title-icon"></i>
                قائمة <span><?= htmlspecialchars($category) ?></span>
            </h1>
            <div class="title-line"></div>
        </div>

        <div class="d-flex justify-content-center flex-wrap flex-menu-tags mb-4">
            <?php 
            foreach($cats_with_icons as $cat_name => $icon_class): 
                $active = ($category == $cat_name) ? 'active' : '';
            ?>
                <a href="category.php?cat=<?= urlencode($cat_name) ?>" class="tag-item <?= $active ?>">
                    <i class="fas <?= $icon_class ?>"></i>
                    <?= $cat_name ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="row justify-content-center">
        <?php
        $stmt = $conn->prepare("SELECT * FROM menuitem WHERE catName=?");
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()):
        ?>
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="product-card">
                    <form action="" class="form-submit">
                        <div class="img-wrapper">
                            <img src="uploads/<?= $row['image'] ?>" alt="<?= $row['itemName'] ?>">
                        </div>
                        
                        <div class="card-content">
                            <div>
                                <h5 class="product-name"><?= $row['itemName'] ?></h5>
                                <div class="product-price">
                                    <?= number_format($row['price']) ?> <small style="font-size: 0.7rem;">ريال</small>
                                </div>
                            </div>

                            <input type="hidden" class="pid" value="<?= $row['itemId'] ?>">
                            <input type="hidden" class="pname" value="<?= $row['itemName'] ?>">
                            <input type="hidden" class="pprice" value="<?= $row['price'] ?>">
                            <input type="hidden" class="pimage" value="<?= $row['image'] ?>">
                            <input type="hidden" class="pcode" value="<?= $row['catName'] ?>">

                            <button class="add-to-cart-btn addItemBtn">
                                <span>أضف للسلة</span>
                                <i class="fas fa-cart-plus"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
        </div>
    </section>
</div>

<div id="toast">
    <i class="fas fa-info-circle ml-2"></i> يجب تسجيل الدخول أولاً.
</div>

<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.min.js'></script>

<script type="text/javascript">
$(document).ready(function() {
    function userIsLoggedIn() {
        return <?= (isset($_SESSION['userloggedin']) && $_SESSION['userloggedin']) ? 'true' : 'false' ?>;
    }

    $(".addItemBtn").click(function(e) {
        e.preventDefault();
        if (!userIsLoggedIn()) {
            $('#toast').fadeIn().delay(3000).fadeOut();
            return;
        }

        var $form = $(this).closest(".form-submit");
        $.ajax({
            url: 'action.php',
            method: 'post',
            data: {
                pid: $form.find(".pid").val(),
                pname: $form.find(".pname").val(),
                pprice: $form.find(".pprice").val(),
                pqty: 1,
                pimage: $form.find(".pimage").val(),
                pcode: $form.find(".pcode").val(),
                email: "<?= $_SESSION['email'] ?? '' ?>"
            },
            success: function(response) {
                $("#message").html(response);
                load_cart_item_number();
                window.scrollTo(0, 0);
            }
        });
    });

    function load_cart_item_number() {
        $.ajax({
            url: 'action.php',
            method: 'get',
            data: { cartItem: "cart_item" },
            success: function(response) {
                $("#cart-item").html(response);
            }
        });
    }
    load_cart_item_number();
});
</script>

<script>
    window.addEventListener("load", function() {
        const loader = document.getElementById("loading-screen");
        
        // إضافة تأخير بسيط (ثانية واحدة) ليعطي انطباعاً بالفخامة ثم يختفي
        setTimeout(() => {
            loader.classList.add("loader-hidden");
        }, 1000); 
    });
</script>
</body>
</html>