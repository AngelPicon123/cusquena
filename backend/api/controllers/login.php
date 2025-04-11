<?php
// Conexión a la base de datos

$host = 'localhost';
$dbname = 'la_cusquena';
$db_username = 'root';
$db_password = '';


if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener datos del formulario
$username = $_POST['username'];
$password = $_POST['password'];

// Buscar al usuario
$sql = "SELECT * FROM usuarios WHERE username = '$username' AND password = '$password'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
    
    // Revisar el cargo
    if ($usuario['rol'] === 'Administrador') {
        echo "Bienvenido administrador";
        // Redirigir o cargar interfaz de admin
    } elseif ($usuario['rol'] === 'Secretaria') {
        echo "Bienvenida secretaria";
        // Redirigir o cargar interfaz de secretaria
    } else {
        echo "Cargo no reconocido";
    }

} else {
    echo "Usuario o contraseña incorrectos";
}

$conn->close();
?>
