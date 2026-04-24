<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sweet";

// Enable error reporting (optional, for debugging)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Establishing connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handling form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collecting form data
    $email = $_POST['email'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $contact = $_POST['contact'];
    $role = $_POST['role'];
    $password = $_POST['password'];
   


    // Prepare SQL statement to insert data into reservations table
    $sql = "INSERT INTO staff (email, firstName, lastName, contact, role, password) 
            VALUES (?, ?, ?, ?, ?, ?)";

    // Prepare and bind parameters
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ssssss", $email, $firstName, $lastName, $contact, $role, $password);

    // Execute the statement
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
                    title: "تمت إضافة الموظفين بنجاح!",
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
                    window.location.href="staffs.php";
                });
            </script>
        </body>
        </html>
        ';
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>