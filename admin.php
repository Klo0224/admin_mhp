<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

    <div class="container">
        <h1>Pending Applications</h1>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Specialization</th>
                    <th>Experience</th>
                    <th>Certificates</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
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

                // Fetch all pending applications
                $sql = "SELECT * FROM doctors WHERE status = 'pending'";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row["id"]) . "</td>
                                <td>" . htmlspecialchars($row["name"]) . "</td>
                                <td>" . htmlspecialchars($row["email"]) . "</td>
                                <td>" . htmlspecialchars($row["specialization"]) . "</td>
                                <td>" . htmlspecialchars($row["experience"]) . "</td>
                                <td>" . htmlspecialchars($row["certificates"]) . "</td>
                                <td>
                                    <a href='update_status.php?id=" . htmlspecialchars($row["id"]) . "&status=approved' class='approve'>Approve</a> |
                                    <a href='update_status.php?id=" . htmlspecialchars($row["id"]) . "&status=declined' class='decline'>Decline</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No pending applications.</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

</body>
</html>
