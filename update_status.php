<?php
include 'connect.php';

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = intval($_GET['id']);
    $status = $_GET['status'] === 'approved' ? 'approved' : 'declined';

    $updateQuery = "UPDATE doctors SET status = '$status' WHERE id = $id";
    if ($conn->query($updateQuery) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid parameters.']);
}

$conn->close();
?>
