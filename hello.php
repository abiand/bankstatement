<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

$totalSteps = 100;
$delayMicroseconds = 10000; // Reduce delay for more frequent updates

for ($i = 1; $i <= $totalSteps; $i++) {
    $percentage = ($i / $totalSteps) * 100;
    echo "data: " . $percentage . "\n\n";
    flush();
    ob_flush();
    usleep($delayMicroseconds);
}

echo "data: 100\n\n";
flush();
ob_flush();
?>