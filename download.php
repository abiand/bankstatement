<?php
// Define the directory where the Excel files are stored
$directory = 'C:\\nginx\\html\\bankstatement\\';
/*
// Function to sanitize the filename
function sanitizeFilename($filename) {
    // Remove any path components
    $filename = basename($filename);

    // Allow only alphanumeric characters, dots, dashes, and underscores
    return preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
} */

// Get the filename from the request
$filename = isset($_GET['file']) ? $_GET['file'] : '';

if ($filename) {
    // Sanitize the filename
   // $filename = sanitizeFilename($filename);

    // Full path to the file
    $filePath = $directory . $filename;

    // Check if the file exists and is an allowed type
    $allowedExtensions = ['xlsx'];
    $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);

    if (file_exists($filePath) && in_array($fileExtension, $allowedExtensions)) {
        // Set headers to force download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Expires: 0');

        // Read the file and output its contents
        readfile($filePath);
        exit;
    } else {
        echo 'File not found or invalid file type.';
    }
} else {
    echo 'No file specified.';
}
?>