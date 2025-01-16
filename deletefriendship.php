<?php
require 'db_connection.php';

$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    $friendshipId = $data['friendshipId'];

    $stmt = $db->prepare("
        DELETE FROM friends WHERE friendship_id = :friendship_id
    ");
    if ($stmt) {
        $stmt->bindValue(':friendship_id', $friendshipId, SQLITE3_INTEGER);
        $stmt->execute();
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to prepare DELETE statement for friends."
        ]);
        exit;
    }

    $stmtMsg = $db->prepare("
        DELETE FROM messages WHERE chatId = :friendship_id
    ");
    if ($stmtMsg) {
        $stmtMsg->bindValue(':friendship_id', $friendshipId, SQLITE3_INTEGER);
        $stmtMsg->execute();
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to prepare DELETE statement for messages."
        ]);
        exit;
    }

    echo json_encode([
        "status" => "success",
        "message" => "Friendship and associated messages deleted successfully.",
        "friendshipId" => $friendshipId
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid input"
    ]);
}
?>
