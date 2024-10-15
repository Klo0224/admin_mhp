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

h2{
    font-weight: bold;
    margin: 0;
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
margin: 10px 0 20px;
}

span {
font-size: 10px;
}

a {
color: #333;
font-size: 14px;
text-decoration: none;
margin: 10px 0;
}

button {
border-radius: 10px;
border: 1px solid #FF4B2B;
background-color: #FF4B2B;
color: #FFFFFF;
font-size: 12px;
font-weight: bold;
padding: 12px 45px;
letter-spacing: 1px;
text-transform: uppercase;
transition: transform 80ms ease-in;
cursor: pointer;
}
.Login{
   
    border-radius: 10px;
    border: 1px solid #d9d9d9;
    background-color: #FFFFFF;
    color: #1cabe3;
    font-size: 12px;
    font-weight: bold;
    padding: 12px 45px;
    letter-spacing: 1px;
    text-transform: uppercase;
    transition: transform 80ms ease-in;
    cursor: pointer;
}
.SignUP{
    
    border-radius: 10px;
    border: 1px solid #d9d9d9;
    background-color: #FFFFFF;
    color: #1cabe3;
    font-size: 12px;
    font-weight: bold;
    padding: 10px 30px;
    letter-spacing: 1px;
    text-transform: uppercase;
    transition: transform 80ms ease-in;
    cursor: pointer;
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
padding: 0 30px;
height: 100%;
text-align: center;
}

input {
background-color: #eee;
border: none;
padding: 12px 15px;
margin: 4px 0;
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
min-height: 480px;
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

footer {
    background-color: #222;
    color: #fff;
    font-size: 14px;
    bottom: 0;
    position: fixed;
    left: 0;
    right: 0;
    text-align: center;
    z-index: 999;
}

footer p {
    margin: 10px 0;
}

footer i {
    color: red;
}

footer a {
    color: #3c97bf;
    text-decoration: none;
}
  </style>
</head>
<body>
  <div class="container" id="container">
    <!-- Sign Up Form -->
    <div class="form-container sign-up-container">
      <form method="post" enctype="multipart/form-data">
        <h1>Create Account</h1>
        <div class="social-container">
          <a href="#" class="social"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="social"><i class="fab fa-google-plus-g"></i></a>
          <a href="#" class="social"><i class="fab fa-linkedin-in"></i></a>
        </div>
        <span>or use your email for registration</span>
        <input type="text" name="full_name" placeholder="Full Name" required />
        <input type="email" name="email" placeholder="Email" required/>
        <input type="text" name="specialization" placeholder="Specialization" required/>
        <input type="number" name="experience" placeholder="Years of Experience" required/>
        <input type="file" name="certificates" accept=".pdf, .jpg, .jpeg, .png, .gif" required/>
        <input type="password" name="password" placeholder="Password" required />
        <input type="submit" class="SignUP" value="Sign Up" name="signUp">
      </form>
    </div>

    <!-- Sign In Form -->
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
        <input type="password" name="password" placeholder="Password" required />
        <a href="forgot_password.html">Forgot your password?</a>
        <input type="submit" class="Login" value="Sign In" name="signIn">
      </form>
    </div>

    <!-- Overlay -->
    <div class="overlay-container">
      <div class="overlay">
        <div class="overlay-panel overlay-left">
          <h1>Welcome Back!</h1>
          <p>To keep connected with us please login with your personal info</p>
          <button class="ghost" id="signIn">Sign In</button>
        </div>
        <div class="overlay-panel overlay-right">
          <h2>Welcome to</h2>
          <h1>Mindsoothe!</h1>
          <p>Begin your journey to inner peace and mental wellness by creating your account.</p>
          <button class="ghost" id="signUp">Sign Up</button>
        </div>
      </div>
    </div>
  </div>

  <!-- JavaScript to handle the switching between login and registration forms -->
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signIn'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check user credentials and approval status
    $query = "SELECT * FROM doctors WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $doctor = $result->fetch_assoc();
        
        if ($doctor['status'] === 'approved') {
            // Doctor is approved
            $_SESSION['doctor_id'] = $doctor['id'];
            $_SESSION['doctor_name'] = $doctor['full_name'];
            echo "<script>
                alert('Welcome! Your account is approved.');
                window.location.href = 'doc_dash.php';
            </script>";
        } elseif ($doctor['status'] === 'pending') {
            // Doctor is not yet approved
            echo "<script>
                alert('Your account is pending approval. Please check back later.');
                window.location.href = 'doclogin.php';
            </script>";
        } else {
            // Doctor is rejected
            echo "<script>
                alert('Your account has been rejected. Please contact the administrator for more information.');
                window.location.href = 'doclogin.php';
            </script>";
        }
    } else {
        // Invalid credentials
        echo "<script>
            alert('Invalid email or password. Please try again.');
            window.location.href = 'doclogin.php';
        </script>";
    }
}
?>