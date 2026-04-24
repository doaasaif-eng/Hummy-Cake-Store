<?php
include 'db_connection.php';

if (isset($_GET['id'])) {
    $itemId = $_GET['id'];

    $sql = "DELETE FROM menuitem WHERE itemId='$itemId'";

    if ($conn->query($sql) === TRUE) {
        echo "تم حذف السجل بنجاح";
    }
    else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();

    header("Location: admin_menu.php");
    exit();
}
?>
