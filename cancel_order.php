<?php
// Include database connection
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $orderId = isset($_POST['orderId']) ? intval($_POST['orderId']) : 0;
    $reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';

    error_log("Received order ID: $orderId, reason: $reason"); // Debugging line

    if ($orderId > 0 && !empty($reason)) {
        $stmt = $conn->prepare("UPDATE orders SET order_status = 'Cancelled', cancel_reason = ? WHERE order_id = ?");
        $stmt->bind_param("si", $reason, $orderId);

        if ($stmt->execute()) {
            echo "تم إلغاء الطلب.";
        } else {
            error_log("خطأ في قاعدة البيانات: " . $stmt->error); // Debugging line
            echo "فشل إلغاء الطلب.";
        }

        $stmt->close();
    } else {
        echo "رقم الطلب أو السبب غير صالح.";
    }
} else {
    echo "طلب غير صالح.";
}

$conn->close();
?>

