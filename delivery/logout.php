<?php
session_start();

// Destroy delivery session
unset($_SESSION['delivery_loggedin']);
unset($_SESSION['delivery_id']);
unset($_SESSION['delivery_name']);
unset($_SESSION['delivery_email']);

// Redirect to delivery login page
header('Location: delivery_login.php');
exit;
?>
