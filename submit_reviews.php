<?php
session_start();
include 'db_connection.php'; // Ensure you have a db_connection.php file to connect to your database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['orderId'];
    $reviewText = $_POST['reviewText'];
    $rating = $_POST['rating'];
    $email = $_SESSION['email']; // Ensure this email is valid and exists in the users table

    // Validate email
    $emailQuery = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $emailQuery->bind_param('s', $email);
    $emailQuery->execute();
    $emailResult = $emailQuery->get_result();
    if ($emailResult->num_rows === 0) {
        die('خطأ: البريد الإلكتروني غير موجود في جدول المستخدمين.');
    }
    $emailQuery->close();

    // Insert or update review
    $stmt = $conn->prepare("INSERT INTO reviews (order_id, email, rating, review_text, response) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE review_text = VALUES(review_text)");
    $stmt->bind_param('isiss', $orderId, $email, $rating, $reviewText, $reviewResponse);

    if ($stmt->execute()) {
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
                    background-color: #d4edda !important;
                    color: #155724 !important;
                    border-color: #c3e6cb !important;
                }
            </style>
        </head>
        <body>
            <script>
                Swal.fire({
                    position: "center",
                    toast: false,
                    title: "تم إرسال التقييم بنجاح!",
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
                    window.location.href="orders.php";
                });
            </script>
        </body>
        </html>
        ';
    } else {
        echo 'Error: ' . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
