<?php
header('Content-Type: application/json');

// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "la_cusquena");

if ($conn->connect_error) {
    echo json_encode(["error" => "Conexión fallida: " . $conn->connect_error]);
    exit;
}

// Obtener fechas del formulario
$inicio = isset($_GET['inicio']) ? $_GET['inicio'] : '';
$fin = isset($_GET['fin']) ? $_GET['fin'] : '';

// Consulta base
$sql = "SELECT 
            vp.descripcion,
            vp.precioUnitario,
            vp.cantidad,
            (vp.precioUnitario * vp.cantidad) AS subtotal,
            vp.fecha
        FROM ventaproducto vp";

// Condición de filtro por fechas
$whereClause = "";
if (!empty($inicio) && !empty($fin)) {
    $whereClause = " WHERE vp.fecha BETWEEN ? AND ?";
}

// Agregar orden descendente por fecha
$orderBy = " ORDER BY vp.fecha DESC";

// Ejecutar consulta
$result = null;
if (!empty($whereClause)) {
    $stmt = $conn->prepare($sql . $whereClause . $orderBy);
    if ($stmt === false) {
        echo json_encode(["error" => "Error al preparar la consulta: " . $conn->error]);
        exit;
    }
    $stmt->bind_param("ss", $inicio, $fin);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql . $orderBy);
    if ($result === false) {
        echo json_encode(["error" => "Error al ejecutar la consulta: " . $conn->error]);
        exit;
    }
}

// Procesar resultados
$productos = [];
while ($row = $result->fetch_assoc()) {
    $fecha = $row['fecha'];
    if (!empty($fecha)) {
        $row['fecha'] = date('d-m-Y', strtotime($fecha));
    } else {
        $row['fecha'] = "No especificada";
    }
    $productos[] = $row;
}

// Enviar respuesta JSON
if (empty($productos)) {
    echo json_encode(["error" => "No se encontraron datos para el rango especificado."]);
} else {
    echo json_encode($productos);
}

// Cerrar conexión
if (isset($stmt)) $stmt->close();
$conn->close();
?>
