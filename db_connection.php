<?php

$dbFile = '/Users/sebastian/vscode/reactjs/chatapp_server/chatappDB.sqlite';
header("Access-Control-Allow-Origin: *"); // Allow requests from any origin
header("Access-Control-Allow-Methods: POST, OPTIONS"); // Allow specific HTTP methods
header("Access-Control-Allow-Headers: Content-Type"); // Allow the Content-Type header

// Handle preflight (OPTIONS) request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Connect to SQLite database
$db = new SQLite3($dbFile);
?>