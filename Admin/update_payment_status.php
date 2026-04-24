<?php
session_start();
if (!isset($_SESSION['adminloggedin'])) {
    header("Location: ../login.php");
    exit();
}

include 'db_connection.php';

// Get POST data
$orderId = isset($_POST['order_id']) ? $_POST['order_id'] : '';
$paymentStatus = isset($_POST['payment_status']) ? $_POST['payment_status'] : '';

// Validate input
if ($orderId && $paymentStatus) {
    // Prepare SQL query to update payment status
    $updateQuery = "UPDATE orders SET payment_status = ? WHERE order_id = ?";
    
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param('si', $paymentStatus, $orderId);
    
    // Execute the query
    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "حدث خطأ أثناء تحديث حالة الدفع.";
    }
    
    $stmt->close();
} else {
    echo "رقم الطلب أو حالة الدفع غير صحيحة.";
}

$conn->close();
?>
