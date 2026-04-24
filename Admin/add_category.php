<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $catName = $conn->real_escape_string($_POST['catName']);
    $dateCreated = date("Y-m-d H:i:s");

    $sql = "INSERT INTO menucategory (catName, dateCreated) VALUES ('$catName', '$dateCreated')";

    if ($conn->query($sql) === TRUE) {
        echo '
        <!DOCTYPE html>
        <html lang="ar">
        <head>
            <meta charset="UTF-8">
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <style>
                body { font-family: "Cairo", sans-serif; background-color: #fdf2e9; }
                .my-custom-popup {
                    padding-top: 40px !important;
                    padding-bottom: 40px !important;
                    font-size: 17px !important;
                    border-radius: 8px !important;
                    background-color: #d4edda !important;
                    color: #155724 !important;
                    border-color: #c3e6cb !important;
                }
            </style>
        </head>
        <body>
            <script>
                Swal.fire({
                    position: "center",
                    toast: false,
                    title: "تمت اضافة القسم بنجاح.",
                    showConfirmButton: false,
                    timer: 2000,
                    width: "300px",
                    customClass: {
                        popup: "my-custom-popup"
                    },
                    showClass: {
                        popup: "animate__animated animate__fadeIn"
                    },
                    hideClass: {
                        popup: "animate__animated animate__fadeOut"
                    }
                }).then(() => {
                    window.location.href="admin_menu.php";
                });
            </script>
        </body>
        </html>
        ';
        exit();
    }
    else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();

    header("Location: admin_menu.php");
    exit();
}
?>
