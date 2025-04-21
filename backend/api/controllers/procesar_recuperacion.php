<?php
session_start();
// Rutas para PHPMailer
// Rutas corregidas para la nueva ubicación
require_once __DIR__ . '/../../includes/phpmailer/PHPMailer.php';
require_once __DIR__ . '/../../includes/phpmailer/SMTP.php';
require_once __DIR__ . '/../../includes/phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
// Conexión a la base de datos
$host = 'localhost';
$dbname = 'la_cusquena';
$db_username = 'root';
$db_password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $db_username, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    
    // 1. Verificar si el email existe
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE correo = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        $_SESSION['error'] = "No existe una cuenta con ese correo electrónico.";
        header("Location: ../../cusquena/frontend/pages/recuperar_contrasena.html");
        exit();
    }

// Después de verificar el email, añade:
$stmt = $conn->prepare("SELECT usuario, rol FROM usuarios WHERE correo = :email");
$stmt->bindParam(':email', $email);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

$_SESSION['recovery_data'] = [
    'correo' => $email,  // <-- Usar 'correo' para ser consistente
    'usuario' => $usuario['usuario'],
    'rol' => $usuario['rol']
];
    // 2. Generar token seguro
    $selector = bin2hex(random_bytes(8));
    $token = random_bytes(32);
    $expires = date("U") + 1800; // 30 minutos de validez
    
    // 3. Eliminar tokens existentes para este email
    $stmt = $conn->prepare("DELETE FROM pwdReset WHERE pwdResetEmail = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    // 4. Insertar nuevo token
    $hashedToken = password_hash($token, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO pwdReset (pwdResetEmail, pwdResetSelector, pwdResetToken, pwdResetExpires) VALUES (:email, :selector, :token, :expires)");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':selector', $selector);
    $stmt->bindParam(':token', $hashedToken);
    $stmt->bindParam(':expires', $expires);
    $stmt->execute();
    
    // 5. Crear URL de reseteo con token
    $url = "http://localhost/cusquena/frontend/pages/contrasena_nueva.php?email=" . urlencode($email);
    
    // 6. Configurar y enviar email con PHPMailer
    $mail = new PHPMailer(true);
    
    try {
        // Configuración SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'jian947200@gmail.com'; // Tu email de envío
        $mail->Password = 'mcvd wyvn zops tpmy'; // Tu contraseña de aplicación
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        
        // Remitente y destinatario
        $mail->setFrom('no-reply@cusquena.com', 'Soporte Cusqueña');
        $mail->addAddress($email);
        
         // Contenido del email CORREGIDO
    $mail->isHTML(true);
    $mail->Subject = 'Restablece tu contraseña de Cusqueña';
    $mail->Body = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Recuperación de contraseña</title>
    </head>
    <body style="font-family: Arial, sans-serif;">
        <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd;">
            <h2 style="color: #0066cc;">Hola,</h2>
            <p>Hemos recibido una solicitud para restablecer tu contraseña.</p>
            <p>Por favor, haz clic en el siguiente enlace para continuar:</p>
            <a href="'.$url.'" 
               style="display: inline-block; background: #0066cc; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 15px 0;">
               Restablecer contraseña
            </a>
            <p><small>Enlace directo: '.$url.'</small></p>
            <p>Este enlace expirará en 30 minutos.</p>
            <p>Si no solicitaste este cambio, ignora este mensaje.</p>
            <hr>
            <p style="font-size: 12px; color: #777;">Equipo de Soporte Cusqueña</p>
        </div>
    </body>
    </html>
    ';
    
    $mail->AltBody = "Para restablecer tu contraseña, visita este enlace: ".$url;
    
    $mail->send();
    $_SESSION['mensaje'] = "¡Hemos enviado un enlace de recuperación a tu correo!";
} catch (Exception $e) {
    error_log('Error al enviar correo: ' . $e->getMessage());
    $_SESSION['error'] = "Ocurrió un error al enviar el correo. Por favor intenta nuevamente.";
}

header("Location: ../../../frontend/pages/recuperar_contrasena.php");
exit();

} else {
    header("Location: ../../../frontend/pages/recuperar_contrasena.php");
    exit();
}