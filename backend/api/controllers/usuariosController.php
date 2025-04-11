<?php
header('Content-Type: application/json');

try {
    $pdo = new PDO("mysql:host=localhost;dbname=la_cusquena", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $accion = $_GET['accion'] ?? $_POST['accion'] ?? '';

    switch ($accion) {
        case 'listar':
            $stmt = $pdo->query("SELECT idUsuario, usuario, correo, rol FROM Usuarios");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        case 'obtener':
            $id = $_GET['id'] ?? 0;
            $stmt = $pdo->prepare("SELECT idUsuario, usuario, correo, rol FROM Usuarios WHERE idUsuario = ?");
            $stmt->execute([$id]);
            echo json_encode($stmt->fetch(PDO::FETCH_ASSOC) ?: []);
            break;

        case 'agregar':
            if (empty($_POST['usuario']) || empty($_POST['correo']) || empty($_POST['rol']) || empty($_POST['contrasena'])) {
                throw new Exception('Todos los campos son requeridos');
            }

            $stmt = $pdo->prepare("INSERT INTO Usuarios (usuario, contrasena, correo, rol) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $_POST['usuario'],
                password_hash($_POST['contrasena'], PASSWORD_DEFAULT),
                $_POST['correo'],
                $_POST['rol']
            ]);

            echo json_encode([
                'status' => 'ok',
                'id' => $pdo->lastInsertId(),
                'message' => 'Usuario creado correctamente'
            ]);
            break;

        case 'editar':
            if (empty($_POST['id']) || empty($_POST['usuario']) || empty($_POST['correo']) || empty($_POST['rol'])) {
                throw new Exception('Todos los campos son requeridos');
            }

            $id = $_POST['id'];
            $datos = [
                'usuario' => $_POST['usuario'],
                'correo' => $_POST['correo'],
                'rol' => $_POST['rol'],
                'id' => $id
            ];

            if (!empty($_POST['contrasena'])) {
                $sql = "UPDATE Usuarios SET usuario = ?, contrasena = ?, correo = ?, rol = ? WHERE idUsuario = ?";
                $datos['contrasena'] = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$datos['usuario'], $datos['contrasena'], $datos['correo'], $datos['rol'], $datos['id']]);
            } else {
                $sql = "UPDATE Usuarios SET usuario = ?, correo = ?, rol = ? WHERE idUsuario = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$datos['usuario'], $datos['correo'], $datos['rol'], $datos['id']]);
            }

            echo json_encode(['status' => 'ok', 'message' => 'Usuario actualizado correctamente']);
            break;

        case 'eliminar':
            $id = $_POST['id'] ?? 0;
            $stmt = $pdo->prepare("DELETE FROM Usuarios WHERE idUsuario = ?");
            $stmt->execute([$id]);
            echo json_encode(['status' => 'ok', 'message' => 'Usuario eliminado correctamente']);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}