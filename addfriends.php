<?php
require 'db_connection.php';

$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    $friendEmail = $data['friendEmail']; 
    $userId = $data['userId'];

    $stmt = $db->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->bindValue(':email', $friendEmail, SQLITE3_TEXT);
    $result = $stmt->execute();
    $friendData = $result->fetchArray(SQLITE3_ASSOC);

    if ($friendData) {
        $friendId = $friendData['id']; 

        try {
            $checkStmt = $db->prepare("
                SELECT friendship_id FROM friends 
                WHERE (userId = :userId AND friendId = :friendId) 
                   OR (userId = :friendId AND friendId = :userId)
            ");
            $checkStmt->bindValue(':userId', $userId, SQLITE3_INTEGER);
            $checkStmt->bindValue(':friendId', $friendId, SQLITE3_INTEGER);
            $existingFriendship = $checkStmt->execute()->fetchArray(SQLITE3_ASSOC);

            if ($existingFriendship) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Friendship already exists.",
                    "friendshipId" => $existingFriendship['friendship_id']
                ]);
                exit;
            }

        
            $insertStmt = $db->prepare("
                INSERT INTO friends (userId, friendId)
                VALUES (:userId, :friendId)
            ");
            $insertStmt->bindValue(':userId', $userId, SQLITE3_INTEGER);
            $insertStmt->bindValue(':friendId', $friendId, SQLITE3_INTEGER);
            $insertStmt->execute();

            $friendshipId = $db->lastInsertRowID();

            echo json_encode([
                "status" => "success",
                "message" => "Friendship created successfully.",
                "friendshipId" => $friendshipId
            ]);
        } catch (Exception $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Failed to create friendship: " . $e->getMessage()
            ]);
        }
    } else {
        // No user with that email
        echo json_encode([
            "status" => "error",
            "message" => "No user found with that email."
        ]);
    }
}
?>
