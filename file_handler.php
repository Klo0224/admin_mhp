<?php
if (isset($_GET['file'])) {
    $file = basename($_GET['file']); // Prevent directory traversal
    $uploadDirectory = __DIR__ . '/uploads/'; // Correctly reference the uploads directory

    // Build the full path
    $filePath = realpath($uploadDirectory . $file);

    // Check if the file exists and is within the uploads directory
    if ($filePath && strpos($filePath, realpath($uploadDirectory)) === 0) {
        if (file_exists($filePath)) {
            // Determine the content type
            $fileType = mime_content_type($filePath);
            header('Content-Description: File Transfer');
            header('Content-Type: ' . $fileType);
            header('Content-Disposition: inline; filename="' . basename($filePath) . '"'); // Change to 'inline' to open in browser
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            exit;
        } else {
            echo "File not found.";
        }
    } else {
        echo "Invalid file.";
    }
} else {
    echo "No file specified.";
}
?>
