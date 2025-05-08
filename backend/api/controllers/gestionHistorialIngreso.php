<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

header('Content-Type: application/json');
include '../../includes/db.php';

if ($conn === null) {
    http_response_code(500);
    echo json_encode(["error" => "No se pudo establecer la conexión a la base de datos."]);
    exit();
}

// LISTAR O BUSCAR INGRESOS DE PRODUCTO
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        if (isset($_GET['buscar']) && !empty(trim($_GET['buscar']))) {
            $buscar = trim($_GET['buscar']);
            // Verificar si el término de búsqueda es un número entero (probable ID)
            if (is_numeric($buscar) && (int)$buscar == $buscar) {
                $stmt = $conn->prepare("SELECT i.idIngresoProducto, i.fechaIngreso, i.cantidad, i.precioCompra, 
                                              i.idProducto, i.detalle, p.descripcion AS productoDescripcion 
                                        FROM IngresoProducto i
                                        LEFT JOIN Producto p ON i.idProducto = p.idProducto
                                        WHERE i.idIngresoProducto = :buscar");
                $stmt->execute(['buscar' => (int)$buscar]);
            } else {
                // Verificar si el término tiene formato dd-mm-yyyy
                if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $buscar, $matches)) {
                    $day = $matches[1];
                    $month = $matches[2];
                    $year = $matches[3];
                    $fechaDB = "$year-$month-$day"; // Convertir a yyyy-mm-dd
                    $buscar = "%$fechaDB%";
                    $stmt = $conn->prepare("SELECT i.idIngresoProducto, i.fechaIngreso, i.cantidad, i.precioCompra, 
                                                  i.idProducto, i.detalle, p.descripcion AS productoDescripcion 
                                            FROM IngresoProducto i
                                            LEFT JOIN Producto p ON i.idProducto = p.idProducto
                                            WHERE i.fechaIngreso LIKE :buscar");
                    $stmt->execute(['buscar' => $buscar]);
                } else {
                    // Búsqueda por descripción del producto si no es ID ni fecha
                    $buscar = "%" . $buscar . "%";
                    $stmt = $conn->prepare("SELECT i.idIngresoProducto, i.fechaIngreso, i.cantidad, i.precioCompra, 
                                                  i.idProducto, i.detalle, p.descripcion AS productoDescripcion 
                                            FROM IngresoProducto i
                                            LEFT JOIN Producto p ON i.idProducto = p.idProducto
                                            WHERE p.descripcion LIKE :buscar");
                    $stmt->execute(['buscar' => $buscar]);
                }
            }
        } else {
            $stmt = $conn->query("SELECT i.idIngresoProducto, i.fechaIngreso, i.cantidad, i.precioCompra, 
                                        i.idProducto, i.detalle, p.descripcion AS productoDescripcion 
                                  FROM IngresoProducto i
                                  LEFT JOIN Producto p ON i.idProducto = p.idProducto");
        }
        
        $ingresos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($ingresos);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error en la consulta: " . $e->getMessage()]);
    }
    exit();
}
// AGREGAR INGRESO DE PRODUCTO
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data['fechaIngreso']) || empty($data['cantidad']) || empty($data['precioCompra']) || empty($data['idProducto'])) {
            http_response_code(400);
            echo json_encode(["error" => "Todos los campos requeridos deben estar completos"]);
            exit();
        }

        $stmt = $conn->prepare("INSERT INTO IngresoProducto (fechaIngreso, cantidad, precioCompra, idProducto, detalle) 
                              VALUES (:fechaIngreso, :cantidad, :precioCompra, :idProducto, :detalle)");
        
        $stmt->execute([
            'fechaIngreso' => $data['fechaIngreso'],
            'cantidad' => (int)$data['cantidad'],
            'precioCompra' => (float)$data['precioCompra'],
            'idProducto' => (int)$data['idProducto'],
            'detalle' => $data['detalle'] ?? ''
        ]);

        echo json_encode(["message" => "Ingreso de producto agregado correctamente"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error al agregar el ingreso: " . $e->getMessage()]);
    }
    exit();
}

// ACTUALIZAR INGRESO DE PRODUCTO
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    try {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data['idIngresoProducto']) || empty($data['fechaIngreso']) || empty($data['cantidad']) || 
            empty($data['precioCompra']) || empty($data['idProducto'])) {
            http_response_code(400);
            echo json_encode(["error" => "Todos los campos requeridos deben estar completos"]);
            exit();
        }

        $stmt = $conn->prepare("UPDATE IngresoProducto SET 
                               fechaIngreso = :fechaIngreso,
                               cantidad = :cantidad, 
                               precioCompra = :precioCompra, 
                               idProducto = :idProducto,
                               detalle = :detalle
                               WHERE idIngresoProducto = :idIngresoProducto");

        $stmt->execute([
            'idIngresoProducto' => (int)$data['idIngresoProducto'],
            'fechaIngreso' => $data['fechaIngreso'],
            'cantidad' => (int)$data['cantidad'],
            'precioCompra' => (float)$data['precioCompra'],
            'idProducto' => (int)$data['idProducto'],
            'detalle' => $data['detalle'] ?? ''
        ]);

        echo json_encode(["message" => "Ingreso de producto actualizado correctamente"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error al actualizar el ingreso: " . $e->getMessage()]);
    }
    exit();
}

// ELIMINAR INGRESO DE PRODUCTO
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    try {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data['idIngresoProducto'])) {
            http_response_code(400);
            echo json_encode(["error" => "El ID del ingreso es requerido"]);
            exit();
        }

        $stmt = $conn->prepare("DELETE FROM IngresoProducto WHERE idIngresoProducto = :idIngresoProducto");
        $stmt->execute(['idIngresoProducto' => (int)$data['idIngresoProducto']]);
        
        echo json_encode(["message" => "Ingreso de producto eliminado correctamente"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error al eliminar el ingreso: " . $e->getMessage()]);
    }
    exit();
}
?>