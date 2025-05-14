<?php
function verificarPermiso($permisosPermitidos) {
    session_start();
    if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], $permisosPermitidos)) {
        header("Location: acceso_denegado.php"); // o cualquier otra acción
        exit();
    }
}
