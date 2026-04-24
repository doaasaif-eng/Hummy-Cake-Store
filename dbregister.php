

<?php
// Establish a connection to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sweet";
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert user details into database
$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$email = $_POST['email'];
$contact = $_POST['contact'];
$password = $_POST['password'];

$sql = "INSERT INTO users (firstName, lastName, email, contact, password) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $firstName, $lastName, $email, $contact, $password);

function showToast($message, $type, $redirect) {
    $bg_color = ($type == 'success') ? '#d4edda' : '#f8d7da';
    $text_color = ($type == 'success') ? '#155724' : '#721c24';
    $border_color = ($type == 'success') ? '#c3e6cb' : '#f5c6cb';

    echo '
    <!DOCTYPE html>
    <html lang="ar">
    <head>
        <meta charset="UTF-8">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <style>
            body { font-family: "Cairo", sans-serif; background-color: #fdf2e9; }
            .my-custom-popup {
                padding-top: 40px !important;
                padding-bottom: 40px !important;
                font-size: 17px !important;
                border-radius: 8px !important;
                background-color: '.$bg_color.' !important;
                color: '.$text_color.' !important;
                border-color: '.$border_color.' !important;
            }
        </style>
    </head>
    <body>
        <script>
            Swal.fire({
                position: "center",
                toast: false,
                title: "'.$message.'",
                showConfirmButton: false,
                timer: 2000,
                width: "300px",
                customClass: {
                    popup: "my-custom-popup"
                },
                showClass: {
                    popup: "animate__animated animate__fadeIn"
                },
                hideClass: {
                    popup: "animate__animated animate__fadeOut"
                }
            }).then(() => {
                window.location.href="'.$redirect.'";
            });
        </script>
    </body>
    </html>
    ';
    exit();
}

try {
    if ($stmt->execute()) {
        showToast("تم التسجيل بنجاح!", "success", "login.php");
    } else {
        $error = $conn->error;
        if (strpos($error, 'Duplicate entry') !== false) {
            showToast("البريد الإلكتروني موجود بالفعل", "error", "login.php");
        }
        throw new Exception($error);
    }
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
        showToast("البريد الإلكتروني موجود بالفعل.", "error", "login.php");
    }
    showToast("فشل التسجيل! المرجو المحاولة لاحقاً.", "error", "login.php");
}

$stmt->close();
$conn->close();
?>
