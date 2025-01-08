<?php
$dbPath = './chatappDB.sqlite'; // Path to your SQLite database

if (file_exists($dbPath)) {
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="chatappDB.sqlite"');
    header('Content-Length: ' . filesize($dbPath));
    readfile($dbPath);
    exit;
} else {
    echo "Database file not found.";
}
?>