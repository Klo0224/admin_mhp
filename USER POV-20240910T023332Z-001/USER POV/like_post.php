<?php
$servername = "localhost";
$username = "root"; // Replace with your DB username
$password = "";     // Replace with your DB password
$dbname = "newsf_db"; // Replace with your DB name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the post ID and the new number of likes from the POST request
$post_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$likes = isset($_POST['likes']) ? intval($_POST['likes']) : 0;

if ($post_id > 0) {
    // Update the likes count for the specified post
    $sql = "UPDATE nf SET likes = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $likes, $post_id);

    if ($stmt->execute()) {
        echo "Likes updated successfully.";
    } else {
        echo "Error updating likes: " . $conn->error;
    }

    $stmt->close();
}
error_log("Post ID: $post_id, Likes: $likes");
$conn->close();
?>
