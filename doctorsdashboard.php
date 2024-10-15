<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Application</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            width: 50%;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
            border-radius: 10px;
        }

        label, input, button {
            display: block;
            width: 100%;
            margin-bottom: 10px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .notification {
            background-color: #f9f9f9;
            padding: 10px;
            border: 1px solid #ccc;
            margin-bottom: 20px;
        }
        
        .notification.unread {
            background-color: #fffbdd;
        }

        .notification.read {
            background-color: #e0f7e9;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Doctor Application Form</h1>

        <!-- Notification bar -->
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

        // Fetch doctor_id from session (dummy value for now)
        $doctor_id = 1;  // Replace this with the actual doctor ID from session when integrated

        // Fetch the latest notification for the doctor
        $sql = "SELECT * FROM notifications WHERE doctor_id = $doctor_id ORDER BY created_at DESC LIMIT 1";
        $notification_result = $conn->query($sql);

        if ($notification_result->num_rows > 0) {
            $row = $notification_result->fetch_assoc();
            $notification_message = $row['message'];
            $notification_status = $row['status'];

            // Display the notification with styling based on its status (read or unread)
            $notification_class = ($notification_status == 'unread') ? 'unread' : 'read';
            echo "<div class='notification $notification_class'>";
            echo "<strong>Notification:</strong> " . $notification_message;
            echo "</div>";

            // Mark notification as read after displaying it
            $update_notification = "UPDATE notifications SET status = 'read' WHERE id = " . $row['id'];
            $conn->query($update_notification);
        } else {
            echo "<div class='notification'>No new notifications.</div>";
        }

        $conn->close();
        ?>

        <form action="submit_application.php" method="POST" enctype="multipart/form-data" onsubmit="return validateFile()">
            <label for="name">Full Name:</label>
            <input type="text" name="name" required><br><br>

            <label for="email">Email:</label>
            <input type="email" name="email" required><br><br>

            <label for="specialization">Specialization:</label>
            <input type="text" name="specialization" required><br><br>

            <label for="experience">Years of Experience:</label>
            <input type="number" name="experience" required><br><br>

            <label for="certificates">Upload Certificate (PDF or Image):</label>
            <input type="file" name="certificates" id="certificates" accept=".pdf, .jpg, .jpeg, .png, .gif" required><br><br>

            <button type="submit">Submit Application</button>
        </form>
    </div>

    <script>
        // Client-side file validation to ensure only images or PDFs are uploaded
        function validateFile() {
            const fileInput = document.getElementById('certificates');
            const filePath = fileInput.value;
            const allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif|\.pdf)$/i;

            if (!allowedExtensions.exec(filePath)) {
                alert('Please upload a valid file (PDF or image).');
                fileInput.value = '';
                return false;
            }
            return true;
        }
    </script>

</body>
</html>
