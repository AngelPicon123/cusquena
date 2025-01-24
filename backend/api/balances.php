<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
// backend/api/balances.php

header("Content-Type: application/json");
include '../includes/db.php';

// Obtener todos los productos para el balance
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $stmt = $conn->query("SELECT * FROM productos");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($productos);
}
?>