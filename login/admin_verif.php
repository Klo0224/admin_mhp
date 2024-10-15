<?php
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

$message = '';

// Registration process
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $specialization = $_POST['specialization'];
    $experience = $_POST['experience'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Check if email already exists
    $check_email = "SELECT * FROM doctors WHERE email = ?";
    $stmt = $conn->prepare($check_email);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $message = "This email is already registered. Please use a different email.";
    } else {
        // Handle license image upload
        $target_dir = "uploads/";
        $certificates = $target_dir . basename($_FILES["certificates"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($certificates,PATHINFO_EXTENSION));
        
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["certificates"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            $message = "File is not an image.";
            $uploadOk = 0;
        }
        
        // Check file size
        if ($_FILES["certificates"]["size"] > 500000) {
            $message = "Sorry, your file is too large.";
            $uploadOk = 0;
        }
        
        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }
        
        // If everything is ok, try to upload file and insert into database
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["certificates"]["tmp_name"], $certificates)) {
                $sql = "INSERT INTO doctors (ful_lname, email, specialization, experience, certificates, password, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssss", $full_name, $email, $specialization, $experience, $certificates, $password);
                
                if ($stmt->execute()) {
                    $message = "Registration successful. Please wait for admin approval.";
                } else {
                    $message = "Error: " . $stmt->error;
                }
            } else {
                $message = "Sorry, there was an error uploading your file.";
            }
        }
    }
}

// Login process
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM doctors WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $doctor = $result->fetch_assoc();
        if (password_verify($password, $doctor['password'])) {
            if ($doctor['status'] === 'approved') {
                $_SESSION['doctor_id'] = $doctor['id'];
                $_SESSION['doctor_name'] = $doctor['full_name'];
                $message = "Login successful!";
            } else {
                $message = "Your account is pending approval. Please wait for admin confirmation.";
            }
        } else {
            $message = "Invalid email or password.";
        }
    } else {
        $message = "Invalid email or password.";
    }
}

// Admin login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_login'])) {
    $admin_username = $_POST['admin_username'];
    $admin_password = $_POST['admin_password'];

    // In a real-world scenario, you'd check these credentials against a database
    if ($admin_username === 'admin' && $admin_password === 'adminpass') {
        $_SESSION['admin'] = true;
        header("Location: ?page=admin");
        exit();
    } else {
        $message = "Invalid admin credentials.";
    }
}

// Admin approval/rejection process
if (isset($_SESSION['admin']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve'])) {
        $doctor_id = $_POST['doctor_id'];
        $sql = "UPDATE doctors SET status = 'approved' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $doctor_id);
        $stmt->execute();
        $message = "Doctor approved successfully.";
    } elseif (isset($_POST['reject'])) {
        $doctor_id = $_POST['doctor_id'];
        $sql = "UPDATE doctors SET status = 'rejected' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $doctor_id);
        $stmt->execute();
        $message = "Doctor rejected successfully.";
    }
}

// Logout process
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Registration and Admin System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .container {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 600px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        input, select {
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        button:hover {
            background-color: #45a049;
        }
        .message {
            color: #333;
            text-align: center;
            margin-top: 10px;
        }
        .toggle-form {
            text-align: center;
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($_SESSION['admin']) && $_GET['page'] === 'admin'): ?>
            <h2>Admin Dashboard</h2>
            <table>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Specialization</th>
                    <th>Experience</th>
                    <th>Action</th>
                </tr>
                <?php
                $sql = "SELECT * FROM doctors WHERE status = 'pending'";
                $result = $conn->query($sql);
                while($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><?php echo $row['full_name']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['specialization']; ?></td>
                    <td><?php echo $row['experience']; ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="doctor_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="approve">Approve</button>
                            <button type="submit" name="reject">Reject</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
            <a href="?logout=1">Logout</a>
        <?php elseif (!isset($_SESSION['admin'])): ?>
            <div id="registrationForm">
                <h2>Doctor Registration</h2>
                <form method="post" enctype="multipart/form-data">
                    <input type="text" name="full_name" placeholder="Full Name" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="text" name="specialization" placeholder="Specialization" required>
                    <input type="number" name="experience" placeholder="Years of Experience" required>
                    <input type="file" name="certificates" accept="image/*" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit" name="register">Register</button>
                </form>
            </div>
            
            <div id="loginForm" style="display:none;">
                <h2>Doctor Login</h2>
                <form method="post">
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit" name="login">Login</button>
                </form>
            </div>

            <div id="adminLoginForm" style="display:none;">
                <h2>Admin Login</h2>
                <form method="post">
                    <input type="text" name="admin_username" placeholder="Admin Username" required>
                    <input type="password" name="admin_password" placeholder="Admin Password" required>
                    <button type="submit" name="admin_login">Login as Admin</button>
                </form>
            </div>

            <?php if ($message): ?>
                <p class="message"><?php echo $message; ?></p>
            <?php endif; ?>

            <div class="toggle-form">
                <a href="#" id="toggleLink">Switch to Login</a> | 
                <a href="#" id="adminToggleLink">Admin Login</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        const registrationForm = document.getElementById('registrationForm');
        const loginForm = document.getElementById('loginForm');
        const adminLoginForm = document.getElementById('adminLoginForm');
        const toggleLink = document.getElementById('toggleLink');
        const adminToggleLink = document.getElementById('adminToggleLink');

        toggleLink.addEventListener('click', function(e) {
            e.preventDefault();
            if (registrationForm.style.display === 'none') {
                registrationForm.style.display = 'block';
                loginForm.style.display = 'none';
                adminLoginForm.style.display = 'none';
                toggleLink.textContent = 'Switch to Login';
            } else {
                registrationForm.style.display = 'none';
                loginForm.style.display = 'block';
                adminLoginForm.style.display = 'none';
                toggleLink.textContent = 'Switch to Registration';
            }
        });

        adminToggleLink.addEventListener('click', function(e) {
            e.preventDefault();
            registrationForm.style.display = 'none';
            loginForm.style.display = 'none';
            adminLoginForm.style.display = 'block';
        });
    </script>
</body>
</html>