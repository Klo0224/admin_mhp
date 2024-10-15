<?php
session_start();
if (!isset($_SESSION['doctor_id'])) {
    header("Location: doctor_login.php");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login_register";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch doctor information
$sql = "SELECT full_name, email, specialization, experience, status FROM doctors WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

$status = $doctor['status'];
$full_name = $doctor['full_name'];
$email = $doctor['email'];
$specialization = $doctor['specialization'];
$experience = $doctor['experience'];

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .alert {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px;
            background-color: green;
            color: white;
            display: none;
            z-index: 1000;
        }
        .alert.declined {
            background-color: red;
        }
        .alert.pending {
            background-color: orange;
        }
    </style>
</head>
<body>
    <div id="alertBox" class="alert"></div>

    <h2>Welcome, <?php echo htmlspecialchars($full_name); ?></h2>
    <p>Email: <?php echo htmlspecialchars($email); ?></p>
    <p>Specialization: <?php echo htmlspecialchars($specialization); ?></p>
    <p>Years of Experience: <?php echo htmlspecialchars($experience); ?></p>
    <p>Status: 
        <?php 
        if ($status == 'approved') {
            echo 'Approved';
        } elseif ($status == 'declined') {
            echo 'Declined';
        } else {
            echo 'Pending Approval';
        }
        ?>
    </p>

    <script>
        // Show in-app alert based on the doctor's status
        var status = '<?php echo $status; ?>';
        var alertBox = document.getElementById('alertBox');

        if (status === 'approved') {
            alertBox.innerHTML = "Congratulations! Your registration is approved.";
            alertBox.style.backgroundColor = "green";
            alertBox.style.display = "block";
            setTimeout(function() {
                alertBox.style.display = "none";
            }, 5000); // Hide after 5 seconds
        } else if (status === 'declined') {
            alertBox.innerHTML = "Sorry, your registration has been declined.";
            alertBox.classList.add('declined');
            alertBox.style.display = "block";
            setTimeout(function() {
                alertBox.style.display = "none";
            }, 5000); // Hide after 5 seconds
        } else {
            alertBox.innerHTML = "Your registration is still pending approval.";
            alertBox.classList.add('pending');
            alertBox.style.display = "block";
            setTimeout(function() {
                alertBox.style.display = "none";
            }, 5000); // Hide after 5 seconds
        }
    </script>
</body>
</html>
