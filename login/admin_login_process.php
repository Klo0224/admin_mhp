<?php
session_start();

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login_register"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inputUsername = $_POST['username'];
    $inputPassword = $_POST['password'];

    // Prepare SQL query to prevent SQL injection
    $stmt = $conn->prepare("SELECT password FROM admin WHERE username = ?");
    $stmt->bind_param("s", $inputUsername);
    $stmt->execute();
    $stmt->store_result();

    // Check if the username exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashedPassword);
        $stmt->fetch();

        // Verify the password
        if (password_verify($inputPassword, $hashedPassword)) {
            // Set session variables
            $_SESSION['admin'] = $inputUsername;
            header("Location: admin_dash.php"); // Redirect to admin dashboard
            exit();
        } else {
            header("Location: admin_login.php?error=Invalid password");
            exit();
        }
    } else {
        header("Location: admin_login.php?error=Invalid username");
        exit();
    }

    $stmt->close();
}

$conn->close();
?>
