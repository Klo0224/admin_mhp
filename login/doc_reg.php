<?php
// Start the session
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login_register"; 

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $specialization = $_POST['specialization'];
    $experience = $_POST['experience'];
    $certificates = $_FILES['certificates']['name'];
    $certificates_tmp = $_FILES['certificates']['tmp_name'];
    
    // Check if email already exists
    $check_email_query = "SELECT * FROM doctors WHERE email = ?";
    $stmt = $conn->prepare($check_email_query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If email exists, show an error message
        echo "<script>alert('An account with this email already exists. Please use a different email.');</script>";
    } else {
        // Move uploaded certificate file
        move_uploaded_file($certificates_tmp, "uploads/" . $certificates);

        // Insert the doctor's data into the database
        $insert_query = "INSERT INTO doctors (full_name, email, specialization, experience, certificates, status) 
                         VALUES (?, ?, ?, ?, ?, 'pending')";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("sssis", $full_name, $email, $specialization, $experience, $certificates);

        if ($stmt->execute()) {
            echo "<script>alert('Registration successful. Your application is pending approval.');</script>";
        } else {
            echo "<script>alert('Registration failed. Please try again.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Registration</title>
</head>
<body>
    <form action="doctor_register.php" method="post" enctype="multipart/form-data">
        <!-- Form fields for registration -->
        <input type="text" name="full_name" placeholder="Full Name" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="text" name="specialization" placeholder="Specialization" required><br>
        <input type="number" name="experience" placeholder="Years of Experience" required><br>
        <input type="file" name="certificates" required><br>
        <button type="submit">Register</button>
    </form>
</body>
</html>
