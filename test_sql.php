<?php
require 'db_connection.php';
$stmt = $conn->prepare("SELECT * FROM orders WHERE delivery_id IS NULL AND order_status IN ('قيد الانتظار', 'Processing') ORDER BY order_date DESC");
if (!$stmt) {
    echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
} else {
    echo "Prepare succeeded";
}
?>
