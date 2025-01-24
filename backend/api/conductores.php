<?php
// backend/api/conductores.php

// Habilitar CORS
header("Access-Control-Allow-Origin: *"); // Permitir solicitudes desde cualquier origen
header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS"); // Permitir los métodos HTTP necesarios
header("Access-Control-Allow-Headers: Content-Type"); // Permitir cabeceras personalizadas

// Si es una solicitud OPTIONS (preflight), terminar la ejecución
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Resto del código...
header("Content-Type: application/json");
include '../includes/db.php';

try {
    // Obtener todos los conductores
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $stmt = $conn->query("SELECT * FROM conductores");
        $conductores = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($conductores);
    }

    // Agregar un nuevo conductor
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);

        $nombre = $data['nombre'];
        $vehiculo = $data['vehiculo'];
        $turno = $data['turno'];
        $deuda = $data['deuda'];
        $detalles = $data['detalles'];

        $stmt = $conn->prepare("INSERT INTO conductores (nombre, vehiculo, turno, deuda, detalles) VALUES (:nombre, :vehiculo, :turno, :deuda, :detalles)");
        $stmt->execute([
            'nombre' => $nombre,
            'vehiculo' => $vehiculo,
            'turno' => $turno,
            'deuda' => $deuda,
            'detalles' => $detalles
        ]);

        echo json_encode(["message" => "Conductor agregado correctamente"]);
    }

    // Actualizar un conductor existente
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        // Obtener los datos del cuerpo de la solicitud
        $data = json_decode(file_get_contents("php://input"), true);

        $id = $data['id'];
        $nombre = $data['nombre'];
        $vehiculo = $data['vehiculo'];
        $turno = $data['turno'];
        $deuda = $data['deuda'];
        $detalles = $data['detalles'];

        $stmt = $conn->prepare("UPDATE conductores SET nombre = :nombre, vehiculo = :vehiculo, turno = :turno, deuda = :deuda, detalles = :detalles WHERE id = :id");
        $stmt->execute([
            'id' => $id,
            'nombre' => $nombre,
            'vehiculo' => $vehiculo,
            'turno' => $turno,
            'deuda' => $deuda,
            'detalles' => $detalles
        ]);

        echo json_encode(["message" => "Conductor actualizado correctamente"]);
    }
} catch (PDOException $e) {
    // Capturar errores de la base de datos
    echo json_encode(["error" => "Error en la base de datos: " . $e->getMessage()]);
} catch (Exception $e) {
    // Capturar otros errores
    echo json_encode(["error" => "Error: " . $e->getMessage()]);
}
?>