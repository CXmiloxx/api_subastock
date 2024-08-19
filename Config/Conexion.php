<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

$servidor = "bnaeekziwkjztwyojaoc-mysql.services.clever-cloud.com";
$usuario = "u3dybgxofmextrb0";
$contrasena = "fHdS3lRqkdKdP7vvSyXa";
$nombre_de_base = "bnaeekziwkjztwyojaoc";

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
