<?php
require 'db_connection.php';
header("Access-Control-Allow-Origin: *"); // Allow requests from any origin
header("Access-Control-Allow-Methods: POST, OPTIONS"); // Allow specific HTTP methods
header("Access-Control-Allow-Headers: Content-Type"); // Allow the Content-Type header
// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    $email = htmlspecialchars($data['email']);
    $password = htmlspecialchars($data['password']);

    // Check if the user exists
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
            "message" => "Invalid email or password"
        ]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
}

// Close the database connection when done
$db->close();
?>