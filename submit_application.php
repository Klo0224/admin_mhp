<?php
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

// Validate form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $specialization = $_POST['specialization'];
    $experience = $_POST['experience'];

    // File upload
    $targetDir = "uploads/";
    $fileName = basename($_FILES["certificates"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    // Allowed file types
    $allowedTypes = array('jpg', 'jpeg', 'png', 'gif', 'pdf');
    
    if (in_array(strtolower($fileType), $allowedTypes)) {
        // Move the uploaded file to the server
        if (move_uploaded_file($_FILES["certificates"]["tmp_name"], $targetFilePath)) {
            // Insert doctor application into the database
            $sql = "INSERT INTO doctors (name, email, specialization, experience, certificates, status) 
                    VALUES ('$name', '$email', '$specialization', '$experience', '$targetFilePath', 'pending')";
            
            if ($conn->query($sql) === TRUE) {
                echo "Application submitted successfully!";
                // Redirect to login page after successful submission
                header("Location: login.html");
                exit();
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "Error uploading the file.";
        }
    } else {
        echo "Invalid file type. Please upload a PDF or image file (jpg, jpeg, png, gif).";
    }
}

$conn->close();
?>
