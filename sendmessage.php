<?php
require 'db_connection.php';
$data = json_decode(file_get_contents("php://input"), true);
if($data){
    $messageBody = $data['messageToSend'];
    $chatId = $data['chatId'];
    $senderId = $data['senderId'];
    $insertStmt = $db->prepare("
                INSERT INTO messages (chatId, senderId, body)
                VALUES (:chatId, :senderId, :body)
            ");
    $insertStmt->bindValue(':chatId', $chatId, SQLITE3_INTEGER);
    $insertStmt->bindValue(':senderId', $senderId, SQLITE3_INTEGER);
    $insertStmt->bindValue(':body', $messageBody, SQLITE3_TEXT);
    $insertStmt->execute();

    echo json_encode([
        "status" => "success",
        "message" => "Message send sucessfully.",
        "message_sent" => $messageBody
    ]);

}else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid input or missing data"
    ]);
}


?>