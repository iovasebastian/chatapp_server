<?php
require 'db_connection.php';

$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    $userId = $data['userId'];

    $stmt = $db->prepare("
        SELECT 
            friendship_id,
            CASE 
                WHEN userId = :userId THEN friendId 
                ELSE userId 
            END AS friendId
        FROM friends
        WHERE userId = :userId OR friendId = :userId
    ");
    $stmt->bindValue(':userId', $userId, SQLITE3_TEXT);
    $result = $stmt->execute();

    $friends = [];

    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $friendId = $row['friendId'];
        $friendshipId = $row['friendship_id'];

        // Fetch friend's details from the users table
        $stmtFriend = $db->prepare("SELECT id, name FROM users WHERE id = :friendId");
        $stmtFriend->bindValue(':friendId', $friendId, SQLITE3_TEXT);
        $resultFriend = $stmtFriend->execute();
        $friendInfo = $resultFriend->fetchArray(SQLITE3_ASSOC);

        if ($friendInfo) {
            $friendInfo['friendship_id'] = $friendshipId;
            $friends[] = $friendInfo;
        }
    }

    if (!empty($friends)) {
        echo json_encode([
            "status" => "success",
            "message" => "Friends retrieved successfully.",
            "friends" => $friends 
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "No friends found."
        ]);
    }
}
?>
