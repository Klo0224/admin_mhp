<?php
$servername = "localhost";
$username = "root"; // Replace with your DB username
$password = "";     // Replace with your DB password
$dbname = "newsf_db"; // Replace with your DB name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, username, message, likes, posted_at FROM nf ORDER BY posted_at DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo '
            <div class="post">
                <div class="post-header">
                    <img src="img/klo.png" alt="User Avatar" class="user-avatar">
                    <span class="username">' . htmlspecialchars($row['username']) . '</span>
                    <span class="time">' . date("M d, Y h:i A", strtotime($row['posted_at'])) . '</span>
                </div>
                <div class="post-content">
                    <p>' . htmlspecialchars($row['message']) . '</p>
                </div>
                <img class="line-2" src="img/line-12-3.svg" />
                <button class="heart-button" data-post-id="' . $row['id'] . '">
                    <span class="heart-icon">&#10084;</span> <span class="likes-count">' . $row['likes'] . '</span>
                </button>
                <p class="reaction-text">Like</p>
            </div>
        ';
    }
} else {
   
}

$conn->close();
?>

