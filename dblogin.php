<?php
session_start();

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Retrieve the email and password from the form
$email    = $_POST['email'];
$password = $_POST['password'];

// Establish a connection to the database
$servername  = "localhost";
$db_username = "root";
$db_password = "";
$dbname      = "sweet";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the email and password match an admin or user
// Prepare the SQL query for users table
$sql_users = "SELECT * FROM users WHERE email = ?";
$stmt_users = $conn->prepare($sql_users);
$stmt_users->bind_param("s", $email);
$stmt_users->execute();
$result_users = $stmt_users->get_result();
$user_data = $result_users->fetch_assoc();

// Prepare the SQL query for staff table
$sql_staff = "SELECT * FROM staff WHERE email = ?";
$stmt_staff = $conn->prepare($sql_staff);
$stmt_staff->bind_param("s", $email);
$stmt_staff->execute();
$result_staff = $stmt_staff->get_result();
$staff_data = $result_staff->fetch_assoc();

try {
    // Check if the login details are correct for users
    if ($user_data && $password === $user_data['password']) {
        // Store user email in session
        $_SESSION['email'] = $email;
        $_SESSION['userloggedin'] = true;

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
                    title: "تم تسجيل دخول المستخدم!",
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
                    window.location.href="menu1.php";
                });
            </script>
        </body>
        </html>
        ';
        exit();
    } 
    // Check if the login details are correct for staff (admin or superadmin)
    else if ($staff_data && $password === $staff_data['password']) {
        $staff = $staff_data;
        if ($staff['role'] === 'superadmin' || $staff['role'] === 'admin') {
            // Store admin email in session
            $_SESSION['email'] = $email;
            $_SESSION['adminloggedin'] = true;

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
                        title: "تم تسجيل دخول المسؤول!",
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
                        window.location.href="Admin/index.php";
                    });
                </script>
            </body>
            </html>
            ';
            exit();
        } else {
            // If the role is not admin or superadmin, redirect to the login page with an error
            header('Location: login.php?error=not_authorized');
            exit();
        }
    } else {
        // Redirect to the login page with an error message
        header('Location: login.php?error');
        exit();
    }
} catch (Exception $e) {
    // Handle the error (e.g., log the error)
    header('Location: login.php?error');
    exit();
}

// Close the connection
$conn->close();
