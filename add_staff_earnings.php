<?php
$conn = new mysqli('localhost', 'root', '', 'sweet');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query('SHOW COLUMNS FROM staff LIKE "earnings"');

if ($result->num_rows == 0) {
    echo "Adding earnings column to staff table...\n";
    if ($conn->query('ALTER TABLE staff ADD COLUMN earnings decimal(10,2) DEFAULT 0')) {
        echo "SUCCESS: earnings column added to staff table!";
    } else {
        echo "ERROR: " . $conn->error;
    }
} else {
    echo "INFO: earnings column already exists in staff table";
}

$conn->close();
?>
