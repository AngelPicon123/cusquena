<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST");
header("Content-Type: application/json");

$pdo = new PDO("mysql:host=localhost;dbname=la_cusquena", "root", "");
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Obtener fechas desde la URL
    $inicio = isset($_GET['inicio']) ? $_GET['inicio'] : '';
    $fin = isset($_GET['fin']) ? $_GET['fin'] : '';

    // Consulta con filtro de fecha
    if ($inicio && $fin) {
        $stmt = $pdo->prepare("SELECT idVenta, idServicio, descripcion, precioUnitario, fechaVenta, total 
                               FROM venta_servicio 
                               WHERE fechaVenta BETWEEN ? AND ?");
        $stmt->execute([$inicio, $fin]);
    } else {
        // Si no se proporcionan fechas, obtener todas las ventas
        $stmt = $pdo->query("SELECT idVenta, idServicio, descripcion, precioUnitario, fechaVenta, total FROM venta_servicio");
    }

    $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($ventas);
} elseif ($method === 'POST') {
    // Registrar una nueva venta
    $data = json_decode(file_get_contents("php://input"), true);
    $sql = "INSERT INTO venta_servicio (idServicio, descripcion, precioUnitario, fechaVenta, total)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([
        $data['idServicio'],
        $data['descripcion'],
        $data['precioUnitario'],
        $data['fechaVenta'],
        $data['total']
    ]);
    echo json_encode(['success' => $success]);
} else {
    echo json_encode(['error' => 'Método no soportado']);
}
