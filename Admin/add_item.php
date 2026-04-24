<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $itemName = $_POST['itemName'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $catName = $_POST['catName'];
    $dateCreated = date("Y-m-d H:i:s");
    $updatedDate = date("Y-m-d H:i:s");

    // File upload handling
    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $uploadOk = 1;

    // Allow all file formats
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if the file is an actual image or fake image (if you still want this check)
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check !== false) {
        // File is an image
        $uploadOk = 1;
    }
    else {
    // Remove the image check if you want to accept any file
    // echo "File is not an image.";
    // $uploadOk = 0;
    }

    // Check file size (you can set your own limit here if needed)
    if ($_FILES["image"]["size"] > 50000000) { // Set to a large value or remove the check
        echo "عذراً، ملفك كبير جداً.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "عذراً، لم يتم تحميل ملفك.";
    // if everything is ok, try to upload file
    }
    else {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // File uploaded successfully, proceed with database insertion
            $image = $_FILES["image"]["name"];

            // Use prepared statements to prevent SQL injection
            $stmt = $conn->prepare("INSERT INTO menuitem (itemName, price, description, image, status, catName, dateCreated, updatedDate) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $itemName, $price, $description, $image, $status, $catName, $dateCreated, $updatedDate);

            if ($stmt->execute()) {
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
                            title: "تمت إضافة العنصر الجديد بنجاح.",
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
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        }
        else {
            echo "عذراً، حدث خطأ أثناء تحميل ملفك.";
        }
    }

    $conn->close();

    // Redirect to admin_menu.php after processing
    header("Location: admin_menu.php");
    exit();
}
?>
