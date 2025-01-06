<?php
require 'db_connection.php';

$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    $friendEmail = $data['friendEmail']; // Friend's email from request
    $userId = $data['userId']; // Current user's ID

    // Step 1: Check if the friend exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->bindValue(':email', $friendEmail, SQLITE3_TEXT);
    $result = $stmt->execute();
    $friendData = $result->fetchArray(SQLITE3_ASSOC);

    if ($friendData) {
        $friendId = $friendData['id']; // Retrieve the friend's ID

        try {
            // Step 2: Check if a friendship already exists (one-to-one uniqueness)
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
                    "friendshipId" => $existingFriendship['friendship_id'] // Optional
                ]);
                exit;
            }

            // Step 3: Add a new friendship
            $insertStmt = $db->prepare("
                INSERT INTO friends (userId, friendId)
                VALUES (:userId, :friendId)
            ");
            $insertStmt->bindValue(':userId', $userId, SQLITE3_INTEGER);
            $insertStmt->bindValue(':friendId', $friendId, SQLITE3_INTEGER);
            $insertStmt->execute();

            // Step 4: Get the friendship_id (used as chatId)
            $friendshipId = $db->lastInsertRowID();

            echo json_encode([
                "status" => "success",
                "message" => "Friendship created successfully.",
                "friendshipId" => $friendshipId // Only friendshipId is sent
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
