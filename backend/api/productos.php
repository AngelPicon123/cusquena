<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Si es una solicitud OPTIONS (preflight), terminar la ejecución
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

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

// Actualizar un producto (venta, primer ingreso o segundo ingreso)
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);

    $id = $data['id'];
    $tipo = $data['tipo']; // Puede ser 'venta', 'primer_ingreso' o 'segundo_ingreso'
    $cantidad = $data['cantidad'];

    // Obtener el producto actual
    $stmt = $conn->prepare("SELECT * FROM productos WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($tipo == 'venta') {
        // Actualizar restantes, vendidos y total
        $restantes = $producto['restantes'] - $cantidad;
        $vendidos = $producto['vendidos'] + $cantidad;
        $total = $producto['total'] + ($cantidad * $producto['precio_venta']);

        $stmt = $conn->prepare("UPDATE productos SET restantes = :restantes, vendidos = :vendidos, total = :total WHERE id = :id");
        $stmt->execute([
            'restantes' => $restantes,
            'vendidos' => $vendidos,
            'total' => $total,
            'id' => $id
        ]);
    } elseif ($tipo == 'primer_ingreso') {
        // Actualizar primer ingreso y restantes
        $primer_ingreso = $producto['primer_ingreso'] + $cantidad;
        $restantes = $producto['restantes'] + $cantidad;

        $stmt = $conn->prepare("UPDATE productos SET primer_ingreso = :primer_ingreso, restantes = :restantes WHERE id = :id");
        $stmt->execute([
            'primer_ingreso' => $primer_ingreso,
            'restantes' => $restantes,
            'id' => $id
        ]);
    } elseif ($tipo == 'segundo_ingreso') {
        // Actualizar segundo ingreso y restantes
        $segundo_ingreso = $producto['segundo_ingreso'] + $cantidad;
        $restantes = $producto['restantes'] + $cantidad;

        $stmt = $conn->prepare("UPDATE productos SET segundo_ingreso = :segundo_ingreso, restantes = :restantes WHERE id = :id");
        $stmt->execute([
            'segundo_ingreso' => $segundo_ingreso,
            'restantes' => $restantes,
            'id' => $id
        ]);
    }

    echo json_encode(["message" => "Producto actualizado correctamente"]);
}
?>