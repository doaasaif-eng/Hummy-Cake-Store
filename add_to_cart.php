<?php
session_start();
require 'db_connection.php';

// Initialize response array
$response = array('status' => '', 'message' => '');

// Check if the email session variable is set
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    // Add products into the cart table
    if (isset($_POST['pid']) && isset($_POST['pname']) && isset($_POST['pprice'])) {
        $pid = $_POST['pid'];
        $pname = $_POST['pname'];
        $pprice = $_POST['pprice'];
        $pimage = $_POST['pimage'];
        $pcode = $_POST['pcode'];
        $pqty = 1;

        $total_price = $pprice * $pqty;

        $stmt = $conn->prepare('SELECT itemName FROM cart WHERE itemName=? AND email=?');
        $stmt->bind_param('ss', $pname, $email);
        $stmt->execute();
        $res = $stmt->get_result();
        $r = $res->fetch_assoc();
        $code = $r['itemName'] ?? '';

        if (!$code) {
            $query = $conn->prepare('INSERT INTO cart (itemName, price, image, quantity, total_price, catName, email) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $query->bind_param('sdsisss', $pname, $pprice, $pimage, $pqty, $total_price, $pcode, $email);
            $query->execute();

            $response['status'] = 'success';
            $response['message'] = 'تمت إضافة المنتج إلى سلة التسوق الخاصة بك!';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'هذا المنتج موجود بالفعل في سلة التسوق الخاصة بك!';
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'بيانات العنصر غير صالحة!';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'المستخدم غير مسجل دخوله!';
}

echo json_encode($response);
?>
