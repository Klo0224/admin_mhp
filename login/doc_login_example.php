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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signUp'])) {
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
        // Handle certificate upload
        $target_dir = "uploads/";
        $certificates = "";
        $uploadOk = 1;
        
        if (isset($_FILES["certificates"]) && $_FILES["certificates"]["error"] == 0) {
            $certificates = $target_dir . basename($_FILES["certificates"]["name"]);
            $imageFileType = strtolower(pathinfo($certificates, PATHINFO_EXTENSION));
            
            // Check if file is an actual image or fake image
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
            && $imageFileType != "gif" && $imageFileType != "pdf") {
                $message = "Sorry, only JPG, JPEG, PNG, GIF & PDF files are allowed.";
                $uploadOk = 0;
            }
            
            // If everything is ok, try to upload file
            if ($uploadOk == 1) {
                if (move_uploaded_file($_FILES["certificates"]["tmp_name"], $certificates)) {
                    // File uploaded successfully
                } else {
                    $message = "Sorry, there was an error uploading your file.";
                    $uploadOk = 0;
                }
            }
        } else {
            $message = "No file was uploaded or there was an error with the upload.";
            $uploadOk = 0;
        }
        
        // If everything is ok, insert into database
        if ($uploadOk == 1) {
            $sql = "INSERT INTO doctors (full_name, email, specialization, experience, certificates, password, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $full_name, $email, $specialization, $experience, $certificates, $password);
            
            if ($stmt->execute()) {
                $message = "Registration successful. Please wait for admin approval.";
            } else {
                $message = "Error: " . $stmt->error;
            }
        }
    }
}


// Login process
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signIn'])) {
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
                header("Location: mhpdashboard.html");
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
  <title>Doctor Login & Registration</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css">
  <style>
    @import url('https://fonts.googleapis.com/css?family=Montserrat:400,800');

    * {
      box-sizing: border-box;
    }

    body {
      background: #f6f5f7;
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      font-family: 'Montserrat', sans-serif;
      height: 100vh;
      margin: -20px 0 50px;
    }

    h1 {
      font-weight: bold;
      margin: 0;
    }

    h2 {
      text-align: center;
    }

    p {
      font-size: 14px;
      font-weight: 100;
      line-height: 20px;
      letter-spacing: 0.5px;
      margin: 20px 0 30px;
    }

    span {
      font-size: 12px;
    }

    a {
      color: #333;
      font-size: 14px;
      text-decoration: none;
      margin: 15px 0;
    }

    button {
      border-radius: 20px;
      border: 1px solid #1cabe3;
      background-color: #1cabe3;
      color: #FFFFFF;
      font-size: 12px;
      font-weight: bold;
      padding: 12px 45px;
      letter-spacing: 1px;
      text-transform: uppercase;
      transition: transform 80ms ease-in;
    }

    button:active {
      transform: scale(0.95);
    }

    button:focus {
      outline: none;
    }

    button.ghost {
      background-color: transparent;
      border-color: #FFFFFF;
    }

    form {
      background-color: #FFFFFF;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      padding: 0 50px;
      height: 100%;
      text-align: center;
    }

    input {
      background-color: #eee;
      border: none;
      padding: 12px 15px;
      margin: 5px 0;
      width: 100%;
    }

    .container {
      background-color: #fff;
      border-radius: 10px;
        box-shadow: 0 14px 28px rgba(0,0,0,0.25), 
          0 10px 10px rgba(0,0,0,0.22);
      position: relative;
      overflow: hidden;
      width: 768px;
      max-width: 100%;
      min-height: 500px;
    }

    .form-container {
      position: absolute;
      top: 0;
      height: 100%;
      transition: all 0.6s ease-in-out;
    }

    .sign-in-container {
      left: 0;
      width: 50%;
      z-index: 2;
    }

    .container.right-panel-active .sign-in-container {
      transform: translateX(100%);
    }

    .sign-up-container {
      left: 0;
      width: 50%;
      opacity: 0;
      z-index: 1;
    }

    .container.right-panel-active .sign-up-container {
      transform: translateX(100%);
      opacity: 1;
      z-index: 5;
      animation: show 0.6s;
    }

    @keyframes show {
      0%, 49.99% {
        opacity: 0;
        z-index: 1;
      }
      
      50%, 100% {
        opacity: 1;
        z-index: 5;
      }
    }

    .overlay-container {
      position: absolute;
      top: 0;
      left: 50%;
      width: 50%;
      height: 100%;
      overflow: hidden;
      transition: transform 0.6s ease-in-out;
      z-index: 100;
    }

    .container.right-panel-active .overlay-container{
      transform: translateX(-100%);
    }

    .overlay {
      background: #FF416C;
      background: linear-gradient(180deg, rgb(200, 185, 250) 14%, rgb(159, 194.5, 244.5) 25%, rgb(28, 171, 227) 64%);
      background-repeat: no-repeat;
      background-size: cover;
      background-position: 0 0;
      color: #FFFFFF;
      position: relative;
      left: -100%;
      height: 100%;
      width: 200%;
        transform: translateX(0);
      transition: transform 0.6s ease-in-out;
    }

    .container.right-panel-active .overlay {
        transform: translateX(50%);
    }

    .overlay-panel {
      position: absolute;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      padding: 0 40px;
      text-align: center;
      top: 0;
      height: 100%;
      width: 50%;
      transform: translateX(0);
      transition: transform 0.6s ease-in-out;
    }

    .overlay-left {
      transform: translateX(-20%);
    }

    .container.right-panel-active .overlay-left {
      transform: translateX(0);
    }

    .overlay-right {
      right: 0;
      transform: translateX(0);
    }

    .container.right-panel-active .overlay-right {
      transform: translateX(20%);
    }

    .social-container {
      margin: 20px 0;
    }

    .social-container a {
      border: 1px solid #DDDDDD;
      border-radius: 50%;
      display: inline-flex;
      justify-content: center;
      align-items: center;
      margin: 0 5px;
      height: 40px;
      width: 40px;
    }
  </style>
</head>
<body>
  <div class="container" id="container">
  <div class="form-container sign-up-container">
      <form method="post" enctype="multipart/form-data">
        <h1>Create Account</h1>
        <div class="social-container">
        </div>
        <input type="text" name="full_name" placeholder="Full Name" required />
        <input type="email" name="email" placeholder="Email" required/>
        <input type="text" name="specialization" placeholder="Specialization" required/>
        <input type="number" name="experience" placeholder="Years of Experience" required/>
        <input type="file" name="certificates" accept=".pdf, .jpg, .jpeg, .png, .gif" required/>
        <input type="password" name="password" placeholder="Password" required />
        <button type="submit" name="signUp">Sign Up</button>
      </form>
    </div>
    <div class="form-container sign-in-container">
      <form method="post">
        <h1>Sign in</h1>
        <div class="social-container">
          <a href="#" class="social"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="social"><i class="fab fa-google-plus-g"></i></a>
          <a href="#" class="social"><i class="fab fa-linkedin-in"></i></a>
        </div>
        <span>or use your account</span>
        <input type="email" name="email" placeholder="Email" required/>
        <input type="password" name="password" placeholder="Password" required/>
        <a href="#">Forgot your password?</a>
        <button type="submit" name="signIn">Sign In</button>
      </form>
    </div>
    <div class="overlay-container">
      <div class="overlay">
        <div class="overlay-panel overlay-left">
          <h1>Welcome Back!</h1>
          <p>To keep connected with us please login with your personal info</p>
          <button class="ghost" id="signIn">Sign In</button>
        </div>
        <div class="overlay-panel overlay-right">
          <h1>Hello, Doctor!</h1>
          <p>Enter your personal details and start journey with us</p>
          <button class="ghost" id="signUp">Sign Up</button>
        </div>
      </div>
    </div>
  </div>
  
  <?php if ($message): ?>
    <p><?php echo $message; ?></p>
  <?php endif; ?>

  <script>
    const signUpButton = document.getElementById('signUp');
    const signInButton = document.getElementById('signIn');
    const container = document.getElementById('container');

    signUpButton.addEventListener('click', () => {
      container.classList.add("right-panel-active");
    });

    signInButton.addEventListener('click', () => {
      container.classList.remove("right-panel-active");
    });
  </script>
</body>
</html>