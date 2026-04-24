<?php
session_start();

// If already logged in as delivery, redirect to dashboard
if (isset($_SESSION['delivery_loggedin']) && $_SESSION['delivery_loggedin']) {
    header('Location: delivery_dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include '../db_connection.php';
    
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Check delivery boy credentials
    $stmt = $conn->prepare("SELECT * FROM staff WHERE email = ? AND role = 'delivery boy'");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // No password hashing, simple text matching as requested
        if ($password === $row['password']) {

            $_SESSION['delivery_loggedin'] = true;
            $_SESSION['delivery_id'] = $row['id'];
            $_SESSION['delivery_name'] = $row['firstName'] . ' ' . $row['lastName'];
            $_SESSION['delivery_email'] = $row['email'];
            header('Location: delivery_dashboard.php');
            exit;
        } else {
            $error = 'كلمة المرور غير صحيحة';
        }
    } else {
        $error = 'البريد الإلكتروني غير مسجل كرجل توصيل';
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل دخول المندوب | Hummy Cake</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        :root {
            --primary: #FF7B54;
            --secondary: #FFB26B;
            --dark: #2D31FA;
            --bg-color: #F9F9F9;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Cairo', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Animated Background Orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: -1;
            animation: float 20s infinite ease-in-out alternate;
        }
        
        .orb-1 {
            width: 400px;
            height: 400px;
            background: rgba(255, 123, 84, 0.4);
            top: -10%;
            right: -10%;
            animation-delay: 0s;
        }

        .orb-2 {
            width: 500px;
            height: 500px;
            background: rgba(255, 178, 107, 0.4);
            bottom: -20%;
            left: -10%;
            animation-delay: -5s;
        }

        .orb-3 {
            width: 300px;
            height: 300px;
            background: rgba(255, 209, 102, 0.3);
            top: 40%;
            left: 30%;
            animation-delay: -10s;
        }

        @keyframes float {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(50px, 50px) scale(1.1); }
            100% { transform: translate(-30px, -20px) scale(0.9); }
        }

        /* Glassmorphism Container */
        .login-container {
            background: rgba(255, 255, 255, 0.65);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 30px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.05), inset 0 0 0 1px rgba(255,255,255,0.5);
            width: 100%;
            max-width: 460px;
            padding: 50px 40px;
            position: relative;
            z-index: 10;
        }

        .header-icon {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            box-shadow: 0 15px 30px rgba(255, 123, 84, 0.3);
            transform: rotate(-5deg);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .login-container:hover .header-icon {
            transform: rotate(0deg) scale(1.05);
        }

        .header-icon i {
            font-size: 45px;
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .login-title {
            text-align: center;
            color: #2D3748;
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }

        .login-subtitle {
            text-align: center;
            color: #718096;
            font-size: 15px;
            margin-bottom: 40px;
            font-weight: 500;
        }

        .form-floating {
            margin-bottom: 25px;
        }

        .form-control {
            border: 2px solid transparent;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 16px;
            height: 60px;
            padding: 1rem 1.25rem;
            font-size: 16px;
            font-weight: 500;
            color: #2D3748;
            transition: all 0.3s ease;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
        }

        .form-control:focus {
            background: #fff;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(255, 123, 84, 0.15);
            outline: 0;
        }

        .form-floating > label {
            padding: 1rem 1.25rem;
            color: #A0AEC0;
            font-weight: 600;
        }

        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label {
            color: var(--primary);
            transform: scale(0.85) translateY(-0.75rem) translateX(0.5rem);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary) 0%, #FF9A62 100%);
            color: white;
            border: none;
            border-radius: 16px;
            width: 100%;
            height: 60px;
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-top: 10px;
            box-shadow: 0 10px 20px rgba(255, 123, 84, 0.3);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0; right: 0; bottom: 0; left: 0;
            background: linear-gradient(135deg, #FF9A62 0%, var(--primary) 100%);
            z-index: -1;
            transition: opacity 0.3s ease;
            opacity: 0;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px rgba(255, 123, 84, 0.4);
            color: white;
        }

        .btn-login:hover::before {
            opacity: 1;
        }

        .btn-login:active {
            transform: translateY(1px);
        }

        .error-message {
            background: rgba(254, 226, 226, 0.9);
            color: #DC2626;
            padding: 15px;
            border-radius: 14px;
            margin-bottom: 25px;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid rgba(252, 165, 165, 0.5);
            animation: headShake 1s;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 30px;
            color: #718096;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: var(--primary);
        }

        .back-link i {
            margin-left: 5px;
            transition: transform 0.3s ease;
        }

        .back-link:hover i {
            transform: translateX(5px);
        }
    </style>
</head>
<body>

    <!-- Animated Background -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="login-container animate__animated animate__fadeInUp">
        <div class="header-icon">
            <i class="fas fa-motorcycle"></i>
        </div>
        
        <h2 class="login-title">لوحة التوصيل</h2>
        <p class="login-subtitle">سجل دخولك لمتابعة طلباتك وأرباحك</p>

        <?php if ($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle fs-5"></i> 
                <span><?php echo $error; ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-floating">
                <input type="email" class="form-control" name="email" id="floatingEmail" placeholder="البريد الإلكتروني" required>
                <label for="floatingEmail"><i class="fas fa-envelope ms-2"></i>البريد الإلكتروني</label>
            </div>
            
            <div class="form-floating">
                <input type="password" class="form-control" name="password" id="floatingPassword" placeholder="كلمة المرور" required>
                <label for="floatingPassword"><i class="fas fa-lock ms-2"></i>كلمة المرور</label>
            </div>
            
            <button type="submit" class="btn-login">
                تسجيل الدخول <i class="fas fa-arrow-left ms-2"></i>
            </button>
        </form>

        <a href="../index.php" class="back-link">
            <i class="fas fa-home"></i> العودة للرئيسية
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
