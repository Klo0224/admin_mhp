<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login_register"; 

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['action']) && isset($_POST['doctor_id'])) {
    $doctor_id = $_POST['doctor_id'];
    $action = $_POST['action'];
    
    // Update the status of the doctor in the database
    if ($action === 'approve') {
        $status = 'approved';
        $message = "You have been approved!";
    } else {
        $status = 'declined';
        $message = "Your application has been declined.";
    }

    $stmt = $conn->prepare("UPDATE doctors SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $doctor_id);
    if ($stmt->execute()) {
        // Store a session variable to display the notification when the doctor logs in
        $_SESSION['doctor_notification'] = $message;

        // Redirect back to the admin dashboard or another appropriate page
        header("Location: admin_dash.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

$conn->close();
?>
