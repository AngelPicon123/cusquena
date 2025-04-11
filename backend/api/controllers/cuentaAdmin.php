<?php
session_start();
echo "<h1>Bienvenido Administrador</h1>";
echo "Usuario: " . $_SESSION['usuario'];
?>
