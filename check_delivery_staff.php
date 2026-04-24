<?php
$conn = new mysqli('localhost', 'root', '', 'sweet');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT id, firstName, lastName, email, role, earnings FROM staff WHERE role = 'delivery boy'");

echo "Delivery Staff:\n";
echo "==============\n";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . "\n";
        echo "Name: " . $row['firstName'] . " " . $row['lastName'] . "\n";
        echo "Email: " . $row['email'] . "\n";
        echo "Role: " . $row['role'] . "\n";
        echo "Earnings: " . $row['earnings'] . "\n";
        echo "-------------------\n";
    }
} else {
    echo "No delivery boys found!\n";
}

$conn->close();
?>
