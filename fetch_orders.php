<?php
session_start();
include 'db_connection.php'; // Ensure you have a db_connection.php file to connect to your database

$email = $_SESSION['email'];
$orderStatus = isset($_GET['status']) ? $_GET['status'] : 'All';

// Map English status to Arabic (for backward compatibility with older orders)
$statusMap = [
    'Pending' => 'قيد الانتظار',
    'Processing' => 'يعالج',
    'On-the-way' => 'في الطريق',
    'On the way' => 'في الطريق',
    'Completed' => 'مكتمل',
    'Cancelled' => 'تم الإلغاء'
];

// Get both English and Arabic status values for filtering
$statusValues = [];
if ($orderStatus !== 'All') {
    $statusValues[] = $orderStatus; // English value
    if (isset($statusMap[$orderStatus])) {
        $statusValues[] = $statusMap[$orderStatus]; // Arabic value
    }
}

// Get all orders for this user
$stmt = $conn->prepare("SELECT orders.*, reviews.review_text, reviews.response,
          staff.firstName AS delivery_fname, staff.lastName AS delivery_lname, staff.contact AS delivery_contact
          FROM orders 
          LEFT JOIN reviews ON orders.order_id = reviews.order_id 
          LEFT JOIN staff ON orders.delivery_id = staff.id
          WHERE orders.email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$orders = [];

while ($order = $result->fetch_assoc()) {
    // Filter by status if needed (supports both English and Arabic)
    if ($orderStatus !== 'All' && !empty($statusValues)) {
        if (!in_array($order['order_status'], $statusValues)) {
            continue; // Skip this order if status doesn't match
        }
    }
    
    $orderId = $order['order_id'];
    
    // Fetch the order items
    $itemsQuery = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $itemsQuery->bind_param('i', $orderId);
    $itemsQuery->execute();
    $itemsResult = $itemsQuery->get_result();
    $order['items'] = $itemsResult->fetch_all(MYSQLI_ASSOC);
    $itemsQuery->close();

    // Include the cancellation reason if the order is cancelled (support both Arabic and English)
    if ($order['order_status'] === 'Cancelled' || $order['order_status'] === 'تم الإلغاء') {
        $cancelQuery = $conn->prepare("SELECT cancel_reason FROM orders WHERE order_id = ?");
        $cancelQuery->bind_param('i', $orderId);
        $cancelQuery->execute();
        $cancelResult = $cancelQuery->get_result();
        $cancelData = $cancelResult->fetch_assoc();
        $order['cancel_reason'] = $cancelData['cancel_reason'];
        $cancelQuery->close();
    }

    // Review information is already included in the main query, no need for extra fetch here
    $orders[] = $order;
}

echo json_encode($orders);

$stmt->close();
$conn->close();
?>
