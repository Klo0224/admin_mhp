<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "doctor_system";

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
        header("Location: admin_dashboard.php");
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
    header("Location: admin_login.php");
    exit();
}

// Dashboard data
$total_doctors = 0;
$pending_doctors = 0;
$approved_doctors = 0;
$rejected_doctors = 0;

if (isset($_SESSION['admin'])) {
    $sql = "SELECT COUNT(*) as total FROM doctors";
    $result = $conn->query($sql);
    $total_doctors = $result->fetch_assoc()['total'];

    $sql = "SELECT COUNT(*) as pending FROM doctors WHERE status = 'pending'";
    $result = $conn->query($sql);
    $pending_doctors = $result->fetch_assoc()['pending'];

    $sql = "SELECT COUNT(*) as approved FROM doctors WHERE status = 'approved'";
    $result = $conn->query($sql);
    $approved_doctors = $result->fetch_assoc()['approved'];

    $sql = "SELECT COUNT(*) as rejected FROM doctors WHERE status = 'rejected'";
    $result = $conn->query($sql);
    $rejected_doctors = $result->fetch_assoc()['rejected'];
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
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
        }
        .container {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .dashboard {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .dashboard-item {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            text-align: center;
            flex: 1;
            margin: 0 10px;
        }
        .dashboard-item h3 {
            margin-top: 0;
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
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        button[name="reject"] {
            background-color: #f44336;
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
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($_SESSION['admin'])): ?>
            <h2>Admin Dashboard</h2>
            <div class="dashboard">
                <div class="dashboard-item">
                    <h3>Total Doctors</h3>
                    <p><?php echo $total_doctors; ?></p>
                </div>
                <div class="dashboard-item">
                    <h3>Pending Approvals</h3>
                    <p><?php echo $pending_doctors; ?></p>
                </div>
                <div class="dashboard-item">
                    <h3>Approved Doctors</h3>
                    <p><?php echo $approved_doctors; ?></p>
                </div>
                <div class="dashboard-item">
                    <h3>Rejected Doctors</h3>
                    <p><?php echo $rejected_doctors; ?></p>
                </div>
            </div>
            <h3>Pending Approvals</h3>
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
                    <td><?php echo $row['fullname']; ?></td>
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
            <div class="logout">
                <a href="?logout=1">Logout</a>
            </div>
        <?php else: ?>
            <h2>Admin Login</h2>
            <form method="post">
                <input type="text" name="admin_username" placeholder="Admin Username" required>
                <input type="password" name="admin_password" placeholder="Admin Password" required>
                <button type="submit" name="admin_login">Login as Admin</button>
            </form>
        <?php endif; ?>

        <?php if ($message): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>