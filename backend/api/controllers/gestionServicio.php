<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

header('Content-Type: application/json');
include '../../includes/db.php'; // Ajusta esta ruta según tu estructura

if ($conn === null) {
    echo json_encode(["error" => "No se pudo establecer la conexión a la base de datos."]);
    exit();
}

// LISTAR SERVICIOS
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['buscar']) && !empty($_GET['buscar'])) {
        $buscar = "%" . $_GET['buscar'] . "%";
        $stmt = $conn->prepare("SELECT * FROM Servicio WHERE idServicio LIKE :buscar OR descripcion LIKE :buscar OR estado LIKE :buscar");
        $stmt->execute(['buscar' => $buscar]);
    } else {
        $stmt = $conn->query("SELECT * FROM Servicio");
    }

    $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($servicios);
}

// AGREGAR SERVICIO O REGISTRAR VENTA
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $accion = $data['accion'] ?? 'agregar';

    if ($accion === 'venta') {
        $fecha = $data['fechaVenta'];
        $total = $data['total'];

        // Registrar la venta
        $stmt = $conn->prepare("INSERT INTO VentaServicio (fechaVenta, total) VALUES (:fechaVenta, :total)");
        $stmt->execute([
            'fechaVenta' => $fecha,
            'total' => $total
        ]);

        echo json_encode(["message" => "Venta registrada correctamente"]);
    } else {
        $descripcion = $data['descripcion'];
        $precioUnitario = $data['precioUnitario'];
        $estado = $data['estado'];

        $stmt = $conn->prepare("INSERT INTO Servicio (descripcion, precioUnitario, estado) VALUES (:descripcion, :precioUnitario, :estado)");
        $stmt->execute([
            'descripcion' => $descripcion,
            'precioUnitario' => $precioUnitario,
            'estado' => $estado
        ]);

        echo json_encode(["message" => "Servicio agregado correctamente"]);
    }
}

// EDITAR SERVICIO
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);

    $idServicio = $data['idServicio'];
    $descripcion = $data['descripcion'];
    $precioUnitario = $data['precioUnitario'];
    $estado = $data['estado'];

    $stmt = $conn->prepare("UPDATE Servicio SET descripcion = :descripcion, precioUnitario = :precioUnitario, estado = :estado WHERE idServicio = :idServicio");
    $stmt->execute([
        'idServicio' => $idServicio,
        'descripcion' => $descripcion,
        'precioUnitario' => $precioUnitario,
        'estado' => $estado
    ]);

    echo json_encode(["message" => "Servicio actualizado correctamente"]);
}

// ELIMINAR SERVICIO
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);
    $idServicio = $data['idServicio'];

    $stmt = $conn->prepare("DELETE FROM Servicio WHERE idServicio = :idServicio");
    $stmt->execute(['idServicio' => $idServicio]);

    echo json_encode(["message" => "Servicio eliminado correctamente"]);
}
