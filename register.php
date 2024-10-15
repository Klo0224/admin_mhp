<?php 
include 'connect.php';

function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

if (isset($_POST['signUp'])) {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $specialization = $_POST['specialization'];
    $experience = $_POST['experience'];
    $password = $_POST['password'];
    $password = md5($password);

    // Handle file upload for certificate
    if (isset($_FILES['certificates']) && $_FILES['certificates']['error'] == 0) {
        $target_dir = "uploads/certificates/"; // Directory where files will be saved
        $certificate_file = $target_dir . basename($_FILES["certificates"]["name"]);
        $uploadOk = 1;
        $fileType = strtolower(pathinfo($certificate_file, PATHINFO_EXTENSION));

        // Check file type (only PDF, JPG, JPEG, PNG, GIF)
        if (!in_array($fileType, ['pdf', 'jpg', 'jpeg', 'png', 'gif'])) {
            echo "Sorry, only PDF, JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["certificates"]["tmp_name"], $certificate_file)) {
                // Validate email syntax
                if (!isValidEmail($email)) {
                    echo "Invalid email format!";
                    exit();
                }

                // Check if the email already exists
                $checkEmail = "SELECT * FROM doctors WHERE email='$email'";
                $result = $conn->query($checkEmail);

                if ($result->num_rows > 0) {
                    echo "Email Address Already Exists!";
                } else {
                    // Insert the doctor's data including the certificate file path
                    $insertQuery = "INSERT INTO doctors (name, email, specialization, experience, certificates, password, status)
                                    VALUES ('$full_name', '$email', '$specialization', '$experience', '$certificate_file', '$password', 'pending')";
                    if ($conn->query($insertQuery) === TRUE) {
                        header("Location: Login.html");
                        exit();
                    } else {
                        echo "Error: " . $conn->error;
                    }
                }
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        echo "Please upload a valid certificate file.";
    }
}

if (isset($_POST['signIn'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password = md5($password);

    // Validate email syntax
    if (!isValidEmail($email)) {
        echo "Invalid email format!";
        exit();
    }

    $sql = "SELECT * FROM doctors WHERE email='$email' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        session_start();
        $row = $result->fetch_assoc();

        if ($row['status'] == 'pending') {
            echo "<script type='text/javascript'>
                alert('Your application is still pending approval.');
                window.location.href = 'Login.html';
                </script>";
        } elseif ($row['status'] == 'approved') {
            $_SESSION['email'] = $row['email'];
            $_SESSION['doctor_id'] = $row['id'];
            header("Location: gracefulThread.php");
            exit();
        } elseif ($row['status'] == 'declined') {
            echo "<script type='text/javascript'>
                alert('Your application has been declined.');
                window.location.href = 'Login.html';
                </script>";
        }
    } else {
        echo "<script type='text/javascript'>
            alert('Not Found, Incorrect Email or Password');
            window.location.href = 'Login.html';
            </script>";
    }
}
?>
