<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
// backend/api/productos.php

header("Content-Type: application/json");
include '../includes/db.php';

// Obtener todos los productos
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $stmt = $conn->query("SELECT * FROM productos");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($productos);
}

// Agregar un nuevo producto
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $nombre = $data['nombre'];
    $categoria = $data['categoria'];
    $precio_compra = $data['precio_compra'];
    $precio_venta = $data['precio_venta'];
    $primer_ingreso = $data['primer_ingreso'];
    $segundo_ingreso = $data['segundo_ingreso'];
    $restantes = $data['restantes'];
    $vendidos = $data['vendidos'];
    $total = $vendidos * $precio_venta;

    $stmt = $conn->prepare("INSERT INTO productos (nombre, categoria, precio_compra, precio_venta, primer_ingreso, segundo_ingreso, restantes, vendidos, total) VALUES (:nombre, :categoria, :precio_compra, :precio_venta, :primer_ingreso, :segundo_ingreso, :restantes, :vendidos, :total)");
    $stmt->execute([
        'nombre' => $nombre,
        'categoria' => $categoria,
        'precio_compra' => $precio_compra,
        'precio_venta' => $precio_venta,
        'primer_ingreso' => $primer_ingreso,
        'segundo_ingreso' => $segundo_ingreso,
        'restantes' => $restantes,
        'vendidos' => $vendidos,
        'total' => $total
    ]);

    echo json_encode(["message" => "Producto agregado correctamente"]);
}
?>