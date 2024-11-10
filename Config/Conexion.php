<?php
require 'vendor/autoload.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

$servidor ='bgpya0xdu9u1yly2o1zd-mysql.services.clever-cloud.com';
$usuario = 'utpggc1aogkjxm5e';
$contrasena = 'VYEI22CEm4gJjydABUdn';
$nombre_de_base = 'bgpya0xdu9u1yly2o1zd';

try {
    $base_de_datos = new PDO("mysql:host=$servidor;dbname=$nombre_de_base", $usuario, $contrasena);
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
        $respuesta['data'] = $datos;
    }
    return $respuesta;
}
?>
