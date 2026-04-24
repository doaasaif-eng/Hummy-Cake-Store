<?php
session_start();
include 'db_connection.php';

// Fetch all unique categories from the database
$categoryQuery = 'SELECT DISTINCT catName FROM menuitem';
$categoryResult = $conn->query($categoryQuery);

$categories = [];
while ($row = $categoryResult->fetch_assoc()) {
    $categories[] = $row['catName'];
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css' />
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css' />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="menu.css" />
    <title>الاطباق</title>

</head>
<body>
    <?php
    // رأس الصفحة يبقى كما هو بتنسيقه الأصلي
    if (isset($_SESSION['userloggedin']) && $_SESSION['userloggedin']) {
        include 'nav-logged.php';
    } else {
        include 'navbar.php';
    }
    ?>

    <div class="heading py-4">
        <div class="row heading-title justify-content-center">قائمة الاطباق</div>
        <div class="row heading-description justify-content-center">اكتشفوا الاطباق من النكهات مع قائمتنا المميزة!</div>
    </div>

    <?php foreach ($categories as $category): ?>
        <section id="<?= strtolower($category) ?>" class="py-4">
            <div id="message"></div>
            <div class="container-fluid">
                <h1 class="mt-1">  <?= strtoupper($category) ?> </h1>
                <div class="row">
                    <?php
                    $stmt = $conn->prepare('SELECT * FROM menuitem WHERE catName = ?');
                    $stmt->bind_param('s', $category);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) :
                        $buttonClass = $row['status'] == 'Unavailable' ? 'disabled-button' : '';
                    ?>
                        <div class="col-md-6 col-lg-3 col-sm-12 menu-item col-xs-12">
                            <div class="product-card">
                                <div class="img-wrapper">
                                    <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['itemName']) ?>">
                                </div>
                                <div class="card-content">
                                    <div>
                                        <h4 class="product-name"><?= htmlspecialchars($row['itemName']) ?></h4>
                                        <p class="product-desc" style="min-height: 60px;"><?= htmlspecialchars($row['description']) ?></p>
                                        
                                        <?php if ($row['status'] == 'Unavailable') : ?>
                                            <div class="card-status"><?php echo htmlspecialchars($row['status']); ?></div>
                                        <?php endif; ?>
                                        
                                        <div class="product-price">
                                            YER&nbsp;<?= number_format($row['price']) ?>
                                        </div>
                                    </div>

                                    <form action="" class="form-submit w-100">
                                        <input type="hidden" class="pid" value='<?= htmlspecialchars($row['id']) ?>'>
                                        <input type="hidden" class="pname" value="<?= htmlspecialchars($row['itemName']) ?>">
                                        <input type="hidden" class="pprice" value="<?= htmlspecialchars($row['price']) ?>">
                                        <input type="hidden" class="pimage" value="<?= htmlspecialchars($row['image']) ?>">
                                        <input type="hidden" class="pcode" value="<?= htmlspecialchars($row['catName']) ?>">
                                        
                                        <button class="add-to-cart-btn addItemBtn <?= htmlspecialchars($buttonClass) ?>" type="button">
                                            إضافة إلى السلة <i class="fas fa-cart-plus"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </section>
    <?php endforeach; ?>

    <div id="toast" class="toast" style="background: rgba(255, 182, 182, 0.9); border: 1px solid rgba(255, 182, 182, 1); font-size: 16px;">
        <button class="toast-btn toast-close">&times;</button>
        <span class="pt-3"><strong>يجب عليك تسجيل الدخول لإضافة عناصر إلى سلة التسوق.</strong></span><br>
        <button class="toast-btn toast-ok">تأكيد</button>
    </div>

    <?php include_once('footer.html'); ?>

    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.min.js'></script>
    <script type="text/javascript">
        $(document).ready(function() {
            function userIsLoggedIn() {
                return <?php echo isset($_SESSION['userloggedin']) && $_SESSION['userloggedin'] === true ? 'true' : 'false'; ?>;
            }

            function showToast() {
                var toast = $('#toast');
                toast.addClass('show');
                setTimeout(function() { toast.removeClass('show'); }, 5000);
            }

            $(".addItemBtn").click(function(e) {
                e.preventDefault();
                if (!userIsLoggedIn()) { showToast(); return; }
                if ($(this).hasClass('disabled-button')) return;

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
                        email: "<?php echo $_SESSION['email'] ?? ''; ?>"
                    },
                    success: function(response) {
                        $("#message").html(response);
                        window.scrollTo(0, 0);
                        load_cart_item_number();
                    }
                });
            });

            $('.toast-close').click(function() { $('#toast').removeClass('show'); });
            $('.toast-ok').click(function() { window.location.href = 'login.php'; });

            function load_cart_item_number() {
                $.ajax({
                    url: 'action.php',
                    method: 'get',
                    data: { cartItem: "cart_item" },
                    success: function(response) { $("#cart-item").html(response); }
                });
            }
            load_cart_item_number();
        });
    </script>
</body>
</html>
