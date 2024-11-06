<?php
include './Config/Conexion.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segmentos_uri = explode('/', $uri);
$idAnimal = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3]) ? $segmentos_uri[3] : null;
$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    try {
        if ($idAnimal) {
            $consulta = $base_de_datos->prepare("
                SELECT * 
                FROM subasta 
                WHERE idAnimal = ?
            ");
            $consulta->execute([$idAnimal]);
            $subasta = $consulta->fetch(PDO::FETCH_ASSOC);

            if ($subasta) {
                $respuesta = formatearRespuesta(true, "El animal ya está en una subasta.", [
                    'subasta' => $subasta
                ]);
            } else {
                $respuesta = formatearRespuesta(false, "El animal no está en ninguna subasta.");
            }
        } else {
            $respuesta = formatearRespuesta(false, "ID de animal no proporcionado o no válido.");
        }
    } catch (Exception $e) {
        $respuesta = formatearRespuesta(false, "Error en la consulta SQL: " . $e->getMessage());
    }
} else {
    $respuesta = formatearRespuesta(false, "Método de solicitud no permitido. Se esperaba GET.");
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");
echo json_encode($respuesta);

?>
