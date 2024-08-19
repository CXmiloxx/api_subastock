<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
require 'vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$servidor = $_ENV['MYSQL_ADDON_HOST'];
$usuario = $_ENV['MYSQL_ADDON_USER'];
$contrasena = $_ENV['MYSQL_ADDON_PASSWORD'];
$nombre_de_base = $_ENV['MYSQL_ADDON_DB'];

try {
    $base_de_datos = new PDO("mysql:host=$servidor; dbname=$nombre_de_base", $usuario, $contrasena);
    $base_de_datos->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    echo json_encode(formatearRespuesta(false, "Error al conectar con la base de datos: " . $e->getMessage()));
    exit;
}

function formatearRespuesta($status, $mensaje, $datos = null) {
    $respuesta = [
        'status' => $status,
        'message' => $mensaje
    ];
    if ($datos !== null) {
        $respuesta = array_merge($respuesta, $datos);
    }
    return $respuesta;
}
?>
