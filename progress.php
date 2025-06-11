<?php
session_start();

// Alternative: Read from the file if session is unreliable
$progress = file_exists("progress.txt") ? file_get_contents("progress.txt") : 0;

echo json_encode(["progress" => $progress]);
?>
