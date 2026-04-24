<?php
$conn = new mysqli('localhost', 'root', '', 'sweet');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT order_id, firstName, lastName, grand_total, delivery_fee, order_status, order_date FROM orders WHERE order_status IN ('مكتمل', 'Completed') ORDER BY order_date DESC LIMIT 10");

echo "Completed Orders with Delivery Fee:\n";
echo "==================================\n";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "Order #" . $row['order_id'] . "\n";
        echo "Customer: " . $row['firstName'] . " " . $row['lastName'] . "\n";
        echo "Total: " . $row['grand_total'] . " | Delivery Fee: " . $row['delivery_fee'] . "\n";
        echo "Status: " . $row['order_status'] . " | Date: " . $row['order_date'] . "\n";
        echo "-------------------\n";
    }
} else {
    echo "No completed orders found!\n";
}

echo "\n\nAll Orders (last 5):\n";
echo "=====================\n";
$result2 = $conn->query("SELECT order_id, grand_total, delivery_fee, order_status FROM orders ORDER BY order_id DESC LIMIT 5");
while ($row = $result2->fetch_assoc()) {
    echo "Order #" . $row['order_id'] . " - Total: " . $row['grand_total'] . " - Delivery Fee: " . $row['delivery_fee'] . " - Status: " . $row['order_status'] . "\n";
}

$conn->close();
?>
