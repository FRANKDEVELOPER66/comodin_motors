<?php

// ✅ FORZAR valor temporalmente para probar
$_ENV['DB_NAME'] = 'comodin_motors';

$host = $_ENV['DB_HOST'] ?? $_ENV['DB_SERVER'] ?? 'mysql-db';
$port = $_ENV['DB_PORT'] ?? '3306';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? 'root';
$database = $_ENV['DB_NAME'] ?? 'comodin_motors';

try {
    $db = new PDO("mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode([
        "detalle" => $e->getMessage(),
        "mensaje" => "Error de conexión a la base de datos",
        "codigo" => 5
    ]);
    exit;
}