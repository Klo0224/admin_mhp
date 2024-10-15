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

// Admin login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_login'])) {
    $admin_username = $_POST['admin_username'];
    $admin_password = $_POST['admin_password'];

    // In a real-world scenario, you'd check these credentials against a database
    if ($admin_username === 'admin' && $admin_password === 'adminpass') {
        $_SESSION['admin'] = true;
        header("Location: ad_loginexample.php");
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
    } elseif (isset($_POST['declined'])) {
        $doctor_id = $_POST['doctor_id'];
        $sql = "UPDATE doctors SET status = 'declined' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $doctor_id);
        $stmt->execute();
    }
}
// Logout process
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin_login.php");
    exit();
}

function isImage($path) {
    if (!file_exists($path)) return false;
    $imageInfo = @getimagesize($path);
    return $imageInfo !== false;
}

function displayDoctorsTable($conn, $status) {
    $sql = "SELECT * FROM doctors WHERE status = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $status);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<table>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Specialization</th>
                    <th>Experience</th>
                    <th>Certificates</th>
                </tr>";
        
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['full_name']) . "</td>
                    <td>" . htmlspecialchars($row['email']) . "</td>
                    <td>" . htmlspecialchars($row['specialization']) . "</td>
                    <td>" . htmlspecialchars($row['experience']) . "</td>
                    <td>";
            
            $certificatePath = $row['certificates'];
            if (isImage($certificatePath)) {
                echo "<a href='#' onclick='showImage(\"" . htmlspecialchars($certificatePath) . "\")' class='certificate-link'>View Certificate</a>";
            } elseif (file_exists($certificatePath)) {
                echo "<a href='" . htmlspecialchars($certificatePath) . "' target='_blank' class='certificate-link'>View Certificate</a>";
            } else {
                echo "<span class='certificate-error'>Certificate not found</span>";
            }
            
            echo "</td>
                </tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p>No " . $status . " doctors found.</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(180deg, rgb(28, 171, 227) 0%, rgb(28, 171, 227) 25%, rgb(159, 194.5, 244.5) 50%, rgb(200, 185, 250) 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .container {
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 1000px;
        }
        .login-container {
            display: flex; /* Add this line */
            flex-direction: column; /* Add this line */
            justify-content: center; /* Add this line */
            align-items: center; /* Add this line */
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            width: 300px;
            margin: auto; /* Center the container horizontally */
        }
        h2 {
            text-align: center;
            color: #333;
        }
        h3 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        td {
            background-color: #fff;
        }
        th {
            background-color: rgba(221, 221, 221, 0.45);
        }
        input[type="text"],
        input[type="password"] {
            background-color: rgba(221, 221, 221, 0.45);
            width: 80%;
            padding: 8px;
            margin: 10px auto;
            border: 2px solid transparent;
            outline: none; 
            border-radius: 4px;
            display: block;
        }
        .button-container {
            display: flex;
            justify-content: center;
            margin-top: 1px;
        }
        button {
            width: 80%;
            padding: 10px;
            margin-bottom: 5px;
            background-color: #67DCC9;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        button[name="declined"] {
            background-color: #FFA7AC;
        }
        .message {
            color: #333;
            text-align: center;
            margin-top: 10px;
        }
        .logout {
            text-align: right;
            margin-top: 20px;
        }
        .certificate-link {
            color: #337088;
            text-decoration: none;
        }
        .certificate-link:hover {
            text-decoration: underline;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.9);
        }
        .modal-content {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
        }
        .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
        }
        .close:hover,
        .close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container">
        <?php if (isset($_SESSION['admin'])): ?>
            <h2>Admin Dashboard</h2>
            
            <h3>Pending Doctors</h3>
            <?php
            $sql = "SELECT * FROM doctors WHERE status = 'pending'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0):
            ?>
            <table>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Specialization</th>
                    <th>Experience</th>
                    <th>Certificates</th>
                    <th>Action</th>
                </tr>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['specialization']); ?></td>
                    <td><?php echo htmlspecialchars($row['experience']); ?></td>
                    <td>
                        <?php
                        $certificatePath = $row['certificates'];
                        if (isImage($certificatePath)):
                        ?>
                            <a href="#" onclick="showImage('<?php echo htmlspecialchars($certificatePath); ?>')" class="certificate-link">View Certificate</a>
                        <?php elseif (file_exists($certificatePath)): ?>
                            <a href="<?php echo htmlspecialchars($certificatePath); ?>" target="_blank" class="certificate-link">View Certificate</a>
                        <?php else: ?>
                            <span class="certificate-error">Certificate not found</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="doctor_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="approve">Approve</button>
                            <button type="submit" name="declined">Reject</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
            <?php else: ?>
                <p>No pending doctors.</p>
            <?php endif; ?>

            <h3>Approved Doctors</h3>
            <?php displayDoctorsTable($conn, 'approved'); ?>

            <h3>Declined Doctors</h3>
            <?php displayDoctorsTable($conn, 'declined'); ?>

            <div class="logout">
                <a href="?logout=1">Logout</a>
            </div>
        <?php else: ?>
            <div class="login-container">
                <h2>Admin Login</h2>
                <form method="post">
                    <input type="text" name="admin_username" placeholder="Admin Username" required>
                    <input type="password" name="admin_password" placeholder="Admin Password" required>
                    <div class="button-container">
                        <button type="submit" name="admin_login">Login as Admin</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <?php if ($message): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
    </div>
    <div id="imageModal" class="modal">
        <span class="close" onclick="closeModal()">&times;</span>
        <img class="modal-content" id="modalImage">
    </div>

    <script>
        function showImage(imagePath) {
            var modal = document.getElementById("imageModal");
            var modalImg = document.getElementById("modalImage");
            modal.style.display = "block";
            modalImg.src = imagePath;
        }

        function closeModal() {
            var modal = document.getElementById("imageModal");
            modal.style.display = "none";
        }

        // Close the modal when clicking outside the image
        window.onclick = function(event) {
            var modal = document.getElementById("imageModal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>