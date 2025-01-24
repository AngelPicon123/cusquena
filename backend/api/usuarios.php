<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
// backend/api/usuarios.php

header("Content-Type: application/json");
include '../includes/db.php';

// Verificar credenciales de usuario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $username = $data['username'];
    $password = $data['password'];

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        echo json_encode(["message" => "Login exitoso"]);
    } else {
        echo json_encode(["message" => "Usuario o contraseña incorrectos"]);
    }
}
?>