<?php
require 'db_connection.php';

$data = json_decode(file_get_contents("php://input"), true);

if ($data && isset($data['chatId'])) {
    $chatId = $data['chatId'];

    $stmt = $db->prepare("SELECT chatId, senderId, body, sent_at FROM messages WHERE chatId = :chatId");
    $stmt->bindValue(':chatId', $chatId, SQLITE3_INTEGER); // Use INTEGER for chatId
    $result = $stmt->execute();

    $messages = []; // Initialize an array to store all messages

    while ($row = $result->fetchArray(SQLITE3_ASSOC)) { // Loop through all messages
        $messages[] = $row;
    }

    echo json_encode([
        "status" => "success",
        "message" => "Messages retrieved successfully.",
        "messages" => $messages // Correct key: "messages"
    ]);
} else {
    // Handle empty or invalid input
    echo json_encode([
        "status" => "error",
        "message" => "Invalid input or missing chatId."
    ]);
}
?>
