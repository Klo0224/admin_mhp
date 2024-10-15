<?php
// Include database connection
$host = 'localhost';
$dbname = 'login_register';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle form submission
if (isset($_POST['signUp'])) {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $specialization = $_POST['specialization'];
    $experience = $_POST['experience'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Handle file upload
    $target_dir = "uploads/";
    $certificate = $_FILES['certificates'];
    $target_file = $target_dir . basename($certificate["name"]);
    $allowed_extensions = ['jpg', 'jpeg', 'png'];
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if (in_array($imageFileType, $allowed_extensions)) {
        if (move_uploaded_file($certificate["tmp_name"], $target_file)) {
            // Insert data into database
            $stmt = $pdo->prepare("INSERT INTO doctors (full_name, email, specialization, experience, certificates, password) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$full_name, $email, $specialization, $experience, $certificate["name"], $password])) {
                header("Location: doclogin.php");
            } else {
                echo "Error: Could not register.";
            }
        } else {
            echo "Error uploading certificate.";
        }
    } else {
        echo "Invalid file type. Only JPG, JPEG, and PNG are allowed.";
    }
}
?>
