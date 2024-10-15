<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Doctor Applications</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .action-buttons a {
            margin-right: 10px;
            text-decoration: none;
            padding: 5px 10px;
            background-color: #28a745;
            color: white;
            border-radius: 5px;
        }
        .action-buttons a.decline {
            background-color: #dc3545;
        }
        img {
            max-width: 100px;
            height: auto;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Doctor Applications</h1>
    <table>
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Email</th>
                <th>Specialization</th>
                <th>Experience</th>
                <th>License</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="doctor-table-body">
            <?php
            include 'connect.php';

            $sql = "SELECT id, name, email, specialization, experience, certificates, status FROM doctors"; 
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr id='doctor-row-{$row['id']}'>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['specialization']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['experience']) . " years</td>";
                    // Check if it's an image or PDF
                    $file_extension = pathinfo($row['certificates'], PATHINFO_EXTENSION);
                    if (in_array(strtolower($file_extension), ['jpg', 'jpeg', 'png', 'gif'])) {
                        echo "<td><img src='" . htmlspecialchars($row['certificates']) . "' alt='Certificate'></td>";
                    } else {
                        echo "<td><a href='" . htmlspecialchars($row['certificates']) . "' target='_blank'>View PDF</a></td>";
                    }
                    echo "<td class='status-cell'>" . ucfirst(htmlspecialchars($row['status'])) . "</td>";
                    echo "<td class='action-buttons'>";
                    if ($row['status'] == 'pending') {
                        echo "<a href='#' class='approve-button' data-id='{$row['id']}'>Approve</a>";
                        echo "<a href='#' class='decline-button decline' data-id='{$row['id']}'>Decline</a>";
                    } else {
                        echo ucfirst(htmlspecialchars($row['status']));
                    }
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No doctor applications found.</td></tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>
</div>

<script>
$(document).ready(function() {
    $('.approve-button').on('click', function(e) {
        e.preventDefault();
        const doctorId = $(this).data('id');
        updateDoctorStatus(doctorId, 'approved');
    });

    $('.decline-button').on('click', function(e) {
        e.preventDefault();
        const doctorId = $(this).data('id');
        updateDoctorStatus(doctorId, 'declined');
    });

    function updateDoctorStatus(doctorId, status) {
        $.ajax({
            url: 'update_status.php',
            type: 'GET',
            data: { id: doctorId, status: status },
            success: function(response) {
                const jsonResponse = JSON.parse(response);
                if (jsonResponse.success) {
                    $('#doctor-row-' + doctorId + ' .status-cell').text(status.charAt(0).toUpperCase() + status.slice(1));
                    $('#doctor-row-' + doctorId + ' .action-buttons').html(status.charAt(0).toUpperCase() + status.slice(1));
                } else {
                    alert('Error updating status: ' + jsonResponse.error);
                }
            },
            error: function() {
                alert('An error occurred while updating the status.');
            }
        });
    }
});
</script>

</body>
</html>
