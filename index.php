<?php
require 'db_connection.php';
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: POST, OPTIONS"); 
header("Access-Control-Allow-Headers: Content-Type"); 

$data = json_decode(file_get_contents("php://input"), true);
if ($data) {
    $email = htmlspecialchars($data['email']);
    $password = htmlspecialchars($data['password']);
    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $result = $stmt->execute();
    $user = $result->fetchArray(SQLITE3_ASSOC);
    
    if ($user && $password==$user['password']) {
        echo json_encode([
            "status" => "success",
            "message" => "Login successful",
            "user" => [
                "id" => $user['id'],
                "email" => $user['email']
            ]
        ]);
    } else {
            echo json_encode([
                "status" => "error",
                "message" => "Invalid email or password",
            ]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
}

$db->close();
?>