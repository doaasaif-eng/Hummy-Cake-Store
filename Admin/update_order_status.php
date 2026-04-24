<?php
session_start();
if (!isset($_SESSION['adminloggedin'])) {
    header("Location: ../login.php");
    exit();
}

include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['order_id'];
    $orderStatus = $_POST['order_status'];
    $cancelReason = $_POST['cancel_reason'] ?? '';

    if ($orderStatus === 'Cancelled' && empty($cancelReason)) {
        $_SESSION['message'] = "سبب الإلغاء مطلوب.";
        header("Location: view.php?orderId=" . $orderId);
        exit();
    }

    $updateQuery = "UPDATE orders SET order_status = ?, cancel_reason = ? WHERE order_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param('ssi', $orderStatus, $cancelReason, $orderId);

    if ($stmt->execute()) {
        $_SESSION['message'] = "تم تحديث حالة الطلب بنجاح.";
    } else {
        $_SESSION['message'] = "فشل تحديث حالة الطلب.";
    }

    header("Location: view_order.php?orderId=" . $orderId);
    exit();
} else {
    header("Location: admin_orders.php");
    exit();
}
?>
