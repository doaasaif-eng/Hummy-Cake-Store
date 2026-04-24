<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
	<link rel="stylesheet" href="login.css" />
	<title>تسجيل الدخول/التسجيل </title>
</head>

<body>

	<?php
	include_once("navbar.php");
	?>

	<div class="container">
		<div class="forms-container">
			<div class="signin-signup">
				<form action="dblogin.php" class="sign-in-form" method="POST">
					<h2 class="title">تسجيل الدخول</h2>
					<div class="input-field">
						<i class="fas fa-envelope"></i>
						<input type="email" placeholder="البريد الالكتروني" name="email" required onkeyup="hideAlertBox()" />
					</div>
					<div class="input-field">
						<i class="fas fa-lock"></i>
						<input type="password" id="loginPassword" placeholder="كلمة المرور" name="password" required onkeyup="hideAlertBox()" />
						<i class="fas fa-eye-slash" id="toggleLoginPassword" style="cursor: pointer;"></i>
					</div>
					<input type="submit" value="تسجيل الدخول" class="submit solid" id="loginButton" />

					<?php

					if (isset($_GET['error'])) {
						echo ('
	        <div class="alert alert-danger" id="alertbox" role="alert">
	        البريد الإلكتروني أو كلمة المرور غير صحيحة.
          </div>');
					}

					?>

				</form>
				<form action="dbregister.php" class="sign-up-form" method="POST" id="registerForm">
					<h2 class="title">أنشأ حساب</h2>
					<div class="input-field">
						<i class="fas fa-user"></i>
						<input type="text" placeholder="الإسم الاول" name="firstName" onkeyup="hideAlertBox()" required />
					</div>
					<div class="input-field">
						<i class="fas fa-user"></i>
						<input type="text" placeholder="الإسم الاخير" name="lastName" onkeyup="hideAlertBox()" required />
					</div>
					<div class="input-field">
						<i class="fas fa-envelope"></i>
						<input type="email" placeholder="البريد الالكتروني" name="email" onkeyup="hideAlertBox()" required />
					</div>
					<div class="input-field">
						<i class="fas fa-phone" style="transform: rotate(90deg);"></i>
						<input type="text" placeholder="رقم الهاتف" name="contact" onkeyup="hideAlertBox()" required />
					</div>
					<div class="input-field">
						<i class="fas fa-lock"></i>
						<input type="password" id="registerPassword" placeholder="كلمه المرور" name="password" required onkeyup="hideAlertBox()" />
						<i class="fas fa-eye-slash" id="toggleRegisterPassword" style="cursor: pointer;"></i>
					</div>
					<input type="submit" class="submit" value="أنشأ حساب" id="registerButton" />



				</form>
			</div>
		</div>

		<div class="panels-container">
			<div class="panel left-panel">
				<div class="content">
					<h3>هل أنت جديد في متجرنا ؟</h3>
					<p>
						انضم إلينا اليوم واستمتع بسهولة الطلب عبر الإنترنت احصل على عروض حصرية وتتبع طلباتك بسهولة.
					</p>
					<button class="btn transparent" id="sign-up-btn">
						انشاء حساب
					</button>
				</div>
				<img src="images/form-pic.png" class="image" alt="" />
			</div>
			<div class="panel right-panel">
				<div class="content">
					<h3>عميلنا؟</h3>
					<p>
						سجّل الدخول لتستمر في الاستمتاع بوجباتنا اللذيذة وإدارة طلباتك بسلاسة.
					</p>
					<button class="btn transparent" id="sign-in-btn">
						تسجيل الدخول
					</button>
				</div>
				<img src="images/form-pic2.png" class="image" alt="" style="margin-bottom: 400px" />
			</div>
		</div>
	</div>

	<script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
	<script>
		// Toggle password visibility for login form
const toggleLoginPassword = document.querySelector('#toggleLoginPassword');
const loginPassword = document.querySelector('#loginPassword');

toggleLoginPassword.addEventListener('click', function() {
    const type = loginPassword.getAttribute('type') === 'password' ? 'text' : 'password';
    loginPassword.setAttribute('type', type);

    // Toggle between eye and eye-slash icons
    if (type === 'password') {
        this.classList.remove('fa-eye');
        this.classList.add('fa-eye-slash');
    } else {
        this.classList.remove('fa-eye-slash');
        this.classList.add('fa-eye');
    }
});

// Toggle password visibility for register form
const toggleRegisterPassword = document.querySelector('#toggleRegisterPassword');
const registerPassword = document.querySelector('#registerPassword');

toggleRegisterPassword.addEventListener('click', function() {
    const type = registerPassword.getAttribute('type') === 'password' ? 'text' : 'password';
    registerPassword.setAttribute('type', type);

    // Toggle between eye and eye-slash icons
    if (type === 'password') {
        this.classList.remove('fa-eye');
        this.classList.add('fa-eye-slash');
    } else {
        this.classList.remove('fa-eye-slash');
        this.classList.add('fa-eye');
    }
});

	</script>

	<script>
		const sign_in_btn = document.querySelector("#sign-in-btn");
		const sign_up_btn = document.querySelector("#sign-up-btn");
		const container = document.querySelector(".container");

		sign_up_btn.addEventListener("click", () => {
			container.classList.add("sign-up-mode");
		});

		sign_in_btn.addEventListener("click", () => {
			container.classList.remove("sign-up-mode");
		});
	</script>
	<script>
		function hideAlertBox() {
			const alertBox = document.getElementById('alertbox');
			alertBox.style.display = 'none';
		}
	</script>

</body>

</html>