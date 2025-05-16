<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Recuperar contraseña</title>
  <link rel="stylesheet" href="../css/recuperar_contraseña.css">
</head>
<body>
  <div class="container">
    <div class="reset-box">
      <h2>Recupera tu contraseña</h2>
      <form id="resetForm">
        <input type="email" id="email" name="email" placeholder="Tu correo electrónico" required>
        <button type="submit">Enviar enlace de recuperación</button>
        <a href="login.html" class="button">Volver</a>
      </form>
      <div id="feedback" class="feedback"></div>
    </div>
  </div>

  <script>
    document.getElementById('resetForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      const email = document.getElementById('email').value.trim();
      if (!email) return alert('Ingresa un correo válido.');

      try {
        const res = await fetch('/backend/api/controllers/procesar_recuperacion.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email })
        });
        const json = await res.json();
        // asume que tu controlador en JSON devuelve { success: bool, message: string }
        const fb = document.getElementById('feedback');
        fb.textContent = json.message;
        fb.className = json.success ? 'success' : 'error';
      } catch (err) {
        console.error(err);
        alert('Error de conexión. Intenta más tarde.');
      }
    });
  </script>
</body>
</html>




