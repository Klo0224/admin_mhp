<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "doctor_applications";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all pending applications
$sql = "SELECT * FROM doctors WHERE status = 'pending'";
$result = $conn->query($sql);

$applications = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $applications[] = $row; // Add each row to the applications array
    }
}

$conn->close();

// Return the applications as JSON
echo json_encode($applications);
?>
