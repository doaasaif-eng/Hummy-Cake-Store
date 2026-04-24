<?php
session_start();
header('Content-Type: application/json');
require 'db_connection.php';

if (!isset($_SESSION['userloggedin']) || $_SESSION['userloggedin'] !== true) {
  echo json_encode(['success' => false, 'message' => 'المستخدم غير مسجل الدخول']);
  exit();
}

$email = $_SESSION['email'];
$id = intval($_POST['id']);

$stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND email = ?");
$stmt->bind_param('is', $id, $email);
$result = $stmt->execute();

if ($result) {
  echo json_encode(['success' => true]);
} else {
  echo json_encode(['success' => false, 'message' => 'فشل حذف العنصر']);
}

$stmt->close();
$conn->close();
?>
