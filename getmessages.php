<?php
require 'db_connection.php';

$data = json_decode(file_get_contents("php://input"), true);

if ($data && isset($data['chatId'])) {
    $chatId = $data['chatId'];

    $stmt = $db->prepare("SELECT chatId, senderId, body, sent_at FROM messages WHERE chatId = :chatId");
    $stmt->bindValue(':chatId', $chatId, SQLITE3_INTEGER);
    $result = $stmt->execute();

    $messages = []; 

    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $messages[] = $row;
    }

    echo json_encode([
        "status" => "success",
        "message" => "Messages retrieved successfully.",
        "messages" => $messages 
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid input or missing chatId."
    ]);
}
?>
