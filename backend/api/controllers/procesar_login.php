<?php
session_start();

// Conexión a la base de datos (FALTA ESTA PARTE CRUCIAL)
$host = 'localhost';
$dbname = 'la_cusquena';
$db_username = 'root';
$db_password = '';

try {
    // ESTA LÍNEA FALTABA - CREAR LA CONEXIÓN PDO
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $db_username, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener los datos en formato JSON
    $data = json_decode(file_get_contents("php://input"), true);

    $usuario = $data['username'] ?? '';
    $contrasena = $data['password'] ?? '';

    if (empty($usuario) || empty($contrasena)) {
        throw new Exception('Usuario y contraseña son requeridos');
    }

    $sql = "SELECT * FROM Usuarios WHERE usuario = :usuario";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':usuario', $usuario);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($contrasena, $user['contrasena'])) {
            $_SESSION['usuario'] = $user['usuario'];
            $_SESSION['rol'] = $user['rol'];

            echo json_encode([
                "success" => true,
                "message" => "Login exitoso",
                "cargo" => $user['rol']
            ]);
        } else {
            throw new Exception('Contraseña incorrecta');
        }
    } else {
        throw new Exception('Usuario no encontrado');
    }
} catch (PDOException $e) {
    error_log("Error en login (PDO): " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => "Error de conexión a la base de datos"
    ]);
} catch (Exception $e) {
    error_log("Error en login: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}   