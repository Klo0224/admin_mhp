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

// Check if message is set
if (isset($_POST['message'])) {
    $message = $conn->real_escape_string($_POST['message']);

    // Insert the message into the database
    $sql = "INSERT INTO nf (username, message) VALUES ('Chloe Kate Realin', '$message')";

    if ($conn->query($sql) === TRUE) {
        $last_id = $conn->insert_id; // Get the ID of the inserted post
        echo '
            <div class="post">
                <div class="post-header">
                    <img src="img/klo.png" alt="User Avatar" class="user-avatar">
                    <span class="username">Chloe Kate Realin</span>
                    <span class="time">Just now</span>
                </div>
                <div class="post-content">
                    <p>' . htmlspecialchars($message) . '</p>
                </div>
                <img class="line-2" src="img/line-12-3.svg" />
                <button class="heart-button" data-post-id="' . $last_id . '">
                    <span class="heart-icon">&#10084;</span> <span class="likes-count">0</span>
                </button>
                <p class="reaction-text">Like</p>
            </div>
        ';
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
