<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login_register"; 

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch doctors based on their status
$pending_result = $conn->query("SELECT * FROM doctors WHERE status = 'pending'");
$approved_result = $conn->query("SELECT * FROM doctors WHERE status = 'approved'");
$declined_result = $conn->query("SELECT * FROM doctors WHERE status = 'declined'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        .pending {
            background-color: #1cabe3;
            color: white;
        }
        .approved {
            background-color: #008000;
            color: white;
        }
        .declined {
            background-color: #FF0000;
            color: white;
        }
        .approve, .decline {
            padding: 5px 10px;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .approve {
            background-color: green;
        }
        .decline {
            background-color: red;
        }
    </style>
</head>
<body>
    <h2>Pending Doctor Registrations</h2>
    <table>
        <tr class="pending">
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Specialization</th>
            <th>Experience</th>
            <th>Certificates</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php while ($doctor = $pending_result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $doctor['id']; ?></td>
            <td><?php echo $doctor['full_name']; ?></td>
            <td><?php echo $doctor['email']; ?></td>
            <td><?php echo $doctor['specialization']; ?></td>
            <td><?php echo $doctor['experience']; ?></td>
            <td>
                <?php if (!empty($doctor['certificates'])): ?>
                    <a href="<?php echo 'uploads/' . htmlspecialchars($doctor['certificates']); ?>" target="_blank">View Certificate</a>
                <?php else: ?>
                    No certificate uploaded.
                <?php endif; ?>
            </td>
            <td><?php echo htmlspecialchars($doctor['status']); ?></td>
            <td>
                <form action="approve_decline.php" method="post" style="display: inline;">
                    <input type="hidden" name="doctor_id" value="<?php echo $doctor['id']; ?>">
                    <button type="submit" name="action" value="approve" class="approve">Approve</button>
                </form>
                <form action="approve_decline.php" method="post" style="display: inline;">
                    <input type="hidden" name="doctor_id" value="<?php echo $doctor['id']; ?>">
                    <button type="submit" name="action" value="decline" class="decline">Decline</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h2>Approved Doctors</h2>
    <table>
        <tr class="approved">
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Specialization</th>
            <th>Experience</th>
            <th>Certificates</th>
            <th>Status</th>
        </tr>
        <?php while ($doctor = $approved_result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $doctor['id']; ?></td>
            <td><?php echo $doctor['full_name']; ?></td>
            <td><?php echo $doctor['email']; ?></td>
            <td><?php echo $doctor['specialization']; ?></td>
            <td><?php echo $doctor['experience']; ?></td>
            <td>
                <?php if (!empty($doctor['certificates'])): ?>
                    <a href="<?php echo 'uploads/' . htmlspecialchars($doctor['certificates']); ?>" target="_blank">View Certificate</a>
                <?php else: ?>
                    No certificate uploaded.
                <?php endif; ?>
            </td>
            <td><?php echo htmlspecialchars($doctor['status']); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h2>Declined Doctors</h2>
    <table>
        <tr class="declined">
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Specialization</th>
            <th>Experience</th>
            <th>Certificates</th>
            <th>Status</th>
        </tr>
        <?php while ($doctor = $declined_result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $doctor['id']; ?></td>
            <td><?php echo $doctor['full_name']; ?></td>
            <td><?php echo $doctor['email']; ?></td>
            <td><?php echo $doctor['specialization']; ?></td>
            <td><?php echo $doctor['experience']; ?></td>
            <td>
                <?php if (!empty($doctor['certificates'])): ?>
                    <a href="<?php echo 'uploads/' . htmlspecialchars($doctor['certificates']); ?>" target="_blank">View Certificate</a>
                <?php else: ?>
                    No certificate uploaded.
                <?php endif; ?>
            </td>
            <td><?php echo htmlspecialchars($doctor['status']); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>
