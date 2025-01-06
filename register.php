<?php
require 'db_connection.php';
$data = json_decode(file_get_contents("php://input"), true);

if($data){
    $email = $data['email'];
    $password = $data['password'];
    $name = $data['name'];

    $stmt = $db->prepare("
    INSERT INTO users (email, password, name)
    VALUES (:email, :password, :name)
    ");

    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $stmt->bindValue(':password', $password, SQLITE3_TEXT);
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);

    $stmt->execute();

    echo json_encode([
        "status" => "success",
        "message" => "Account created succesfully.", 
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid input"
    ]);
}

?>