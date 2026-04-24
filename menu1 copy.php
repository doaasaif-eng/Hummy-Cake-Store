<?php 
session_start(); 
include 'db_connection.php'; 

/* جلب منتج واحد من كل قسم لعرض الأقسام المتوفرة */ 
$query = "SELECT * FROM menuitem GROUP BY catName"; 
$result = $conn->query($query); 
?> 
<!DOCTYPE html> 
<html lang="ar"> 
<head> 
<meta charset="UTF-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>قائمة الأطباق - عرض احترافي</title> 

<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css' /> 
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css' /> 

<style>
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap');

    body {
        background-color: #fdf2e9; 
        font-family: 'Cairo', sans-serif;
        overflow-x: hidden;
        color: #2c1a1a;
    }

    /* --- قسم العنوان الرئيسي --- */
    .hero-title-section {
        text-align: center;
        padding: 80px 0 40px;
    }
    .hero-title-section h2 {
        font-weight: 900;
        color: #2c1a1a;
        font-size: 3.5rem;
        margin-bottom: 10px;
    }
    .custom-underline {
        width: 100px;
        height: 5px;
        background: #fb4a36;
        margin: 15px auto;
        border-radius: 50px;
    }

    /* --- تنسيق الكرت باللون المطلوب #ffbda1 --- */
    .product-card {
        background: #fed6c5; /* اللون الذي طلبته */
        border-radius: 22px;
        padding: 15px;
        overflow: hidden;
        border: none;
        /* ظل أعمق قليلاً ليتناسب مع اللون البرتقالي الفاتح */
        box-shadow: 0 8px 25px rgba(44, 26, 26, 0.1); 
        transition: all 0.4s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .product-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(251, 74, 54, 0.2);
        /* تفتيح اللون قليلاً عند الهوفر لإعطاء إحساس بالتفاعل */
        background: #ffc9b3; 
    }

    /* صندوق الصورة */
    .img-wrapper {
        width: 100%;
        height: 220px;
        border-radius: 18px;
        overflow: hidden;
        background: #fff;
    }
    .img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: 0.8s;
    }
    .product-card:hover .img-wrapper img {
        transform: scale(1.1);
    }

    /* محتوى الكرت */
    .card-content {
        padding: 20px 5px 5px 5px;
        text-align: center;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .product-name {
        font-size: 1.5rem;
        font-weight: 800;
        color: #2c1a1a; /* لون بني داكن للتباين مع الخلفية */
        margin-bottom: 10px;
    }

    .category-desc {
        color: #4a3a3a; /* تغميق لون الوصف ليكون واضحاً على الخلفية الملونة */
        font-size: 0.95rem;
        margin-bottom: 20px;
        line-height: 1.5;
        font-weight: 600;
    }

    /* زر استكشف - متناسق مع كرت المنتجات */
    .btn-explore-styled {
        background: #2c1a1a;
        color: #fff !important;
        border: none;
        width: 100%;
        padding: 12px;
        border-radius: 12px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: 0.3s;
    }

    .product-card:hover .btn-explore-styled {
        background: #fb4a36;
    }

    .btn-explore-styled i {
        font-size: 0.8rem;
        transition: 0.3s;
    }

    .product-card:hover .btn-explore-styled i {
        transform: translateX(-5px);
    }

    .menu-grid-container {
        padding: 20px 40px 100px;
    }
</style>
</head> 
<body> 

<?php 
if (isset($_SESSION['userloggedin']) && $_SESSION['userloggedin']) { 
    include 'nav-logged.php'; 
} else { 
    include 'navbar.php'; 
} 
?> 

<div class="container-fluid">
    <div class="hero-title-section">
        <h2>قائمة الأطباق</h2>
        <div class="custom-underline"></div>
        <p>اكتشف تشكيلتنا الواسعة المحضرة بكل شغف</p>
    </div>

    <div class="menu-grid-container">
        <div class="row justify-content-center"> 
            <?php if($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?> 
                    <div class="col-xl-3 col-lg-4 col-md-6 mb-5"> 
                        <a href="category.php?cat=<?= urlencode($row['catName']) ?>" style="text-decoration:none;">
                            <div class="product-card"> 
                                <div class="img-wrapper">
                                    <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['catName']) ?>"> 
                                </div>
                                
                                <div class="card-content"> 
                                    <div>
                                        <h5 class="product-name"><?= htmlspecialchars($row['catName']) ?></h5> 
                                        <p class="category-desc">أشهى وجبات <?= htmlspecialchars($row['catName']) ?> المختارة بعناية لتناسب ذوقكم.</p> 
                                    </div>
                                    
                                    <div class="btn-explore-styled">
                                        <span>استكشف القسم</span>
                                        <i class="fas fa-arrow-left"></i>
                                    </div>
                                </div> 
                            </div> 
                        </a>
                    </div> 
                <?php endwhile; ?> 
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="text-muted">لا توجد أقسام متوفرة حالياً.</p>
                </div>
            <?php endif; ?>
        </div> 
    </div>
</div> 

<?php include 'footer.html'; ?> 

</body> 
</html>