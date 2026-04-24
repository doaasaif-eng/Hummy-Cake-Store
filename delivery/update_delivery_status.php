<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['delivery_loggedin']) || !$_SESSION['delivery_loggedin']) {
    echo json_encode(['success' => false, 'message' => 'غير مسجل الدخول']);
    exit;
}

include '../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = intval($_POST['order_id']);
    $status = trim($_POST['status']);

    // Allowed English statuses only (ENUM now uses English values)
    $allowed_statuses = ['Pending', 'Processing', 'On the way', 'Completed', 'Cancelled'];

    // Map Arabic status names to English (in case old calls use Arabic)
    $status_map = [
        'قيد الانتظار' => 'Pending',
        'يعالج'        => 'Processing',
        'في الطريق'   => 'On the way',
        'مكتمل'       => 'Completed',
        'تم الإلغاء'  => 'Cancelled'
    ];

    if (isset($status_map[$status])) {
        $status = $status_map[$status];
    }

    if (!in_array($status, $allowed_statuses)) {
        echo json_encode(['success' => false, 'message' => 'حالة غير صالحة: ' . $status]);
        exit;
    }

    if ($status === 'On the way') {
        // Assign this delivery person to the order (only if unassigned)
        $delivery_id = intval($_SESSION['delivery_id']);
        $stmt = $conn->prepare("UPDATE orders SET order_status = ?, delivery_id = ? WHERE order_id = ? AND delivery_id IS NULL");
        $stmt->bind_param('sii', $status, $delivery_id, $order_id);
    } else {
        $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
        $stmt->bind_param('si', $status, $order_id);
    }

    if ($stmt->execute()) {
        if ($status === 'Completed') {
            // Mark payment as successful
            $updatePayment = $conn->prepare("UPDATE orders SET payment_status = 'Successful' WHERE order_id = ?");
            $updatePayment->bind_param('i', $order_id);
            $updatePayment->execute();
            $updatePayment->close();

            // Get delivery fee and assigned delivery_id from the order
            $getOrder = $conn->prepare("SELECT delivery_fee, delivery_id FROM orders WHERE order_id = ?");
            $getOrder->bind_param('i', $order_id);
            $getOrder->execute();
            $orderData = $getOrder->get_result()->fetch_assoc();
            $getOrder->close();

            if ($orderData && $orderData['delivery_id']) {
                $deliveryFee = floatval($orderData['delivery_fee']);
                $assigned_delivery_id = intval($orderData['delivery_id']);

                // Add delivery fee to the delivery person's earnings
                $updateEarnings = $conn->prepare("UPDATE staff SET earnings = earnings + ? WHERE id = ?");
                $updateEarnings->bind_param('di', $deliveryFee, $assigned_delivery_id);
                $updateEarnings->execute();
                $updateEarnings->close();
            }
        }

        echo json_encode(['success' => true, 'message' => 'تم تحديث حالة الطلب بنجاح']);
    } else {
        echo json_encode(['success' => false, 'message' => 'فشل تحديث حالة الطلب: ' . $conn->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'طريقة طلب غير صالحة']);
}

$conn->close();
?>
