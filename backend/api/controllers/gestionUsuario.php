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
    echo json_encode(["error" => "No se pudo establecer la conexión a la base de datos."]);
    exit();
}

// LISTAR o BUSCAR
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $query = "SELECT * FROM usuarios";

    if (isset($_GET['buscar']) && !empty($_GET['buscar'])) {
        $buscar = "%" . $_GET['buscar'] . "%";
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario LIKE :buscar OR correo LIKE :buscar");
        $stmt->execute(['buscar' => $buscar]);
    } else {
        $stmt = $conn->query($query);
    }

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($usuarios);
}

// AGREGAR USUARIO
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    var_dump($data);

    if (!isset($data['usuario']) || !isset($data['contraseña']) || !isset($data['correo']) || !isset($data['rol']) || !isset($data['estado'])) {
        echo json_encode(["error" => "Faltan datos necesarios"]);
        exit();
    }

    $usuario = $data['usuario'];
    $contraseña = $data['contraseña']; // Opcional: aplicar password_hash
    $correo = $data['correo'];
    $rol = $data['rol'];
    $estado = $data['estado'];

    $stmt = $conn->prepare("INSERT INTO usuarios (usuario, contraseña, correo, rol, estado) VALUES (:usuario, :contraseña, :correo, :rol, :estado)");
    $stmt->execute([
        ':usuario' => $usuario,
        ':contraseña' => $contraseña,
        ':correo' => $correo,
        ':rol' => $rol,
        ':estado' => $estado
    ]);

    echo json_encode(["message" => "Usuario agregado correctamente"]);
}

// ACTUALIZAR USUARIO
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);

    $id = $data['id'];
    $usuario = $data['usuario'];
    $contraseña = $data['contraseña'];
    $correo = $data['correo'];
    $rol = $data['rol'];
    $estado = $data['estado'];

    $stmt = $conn->prepare("UPDATE usuarios SET usuario = :usuario, contraseña = :contraseña, correo = :correo, rol = :rol, estado = :estado WHERE id = :id");
    $stmt->execute([
        'id' => $id,
        'usuario' => $usuario,
        'contraseña' => $contraseña,
        'correo' => $correo,
        'rol' => $rol,
        'estado' => $estado
    ]);

    echo json_encode(["message" => "Usuario actualizado correctamente"]);
}

// ELIMINAR USUARIO
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];

    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = :id");
    $stmt->execute(['id' => $id]);

    echo json_encode(["message" => "Usuario eliminado correctamente"]);
}
?>
