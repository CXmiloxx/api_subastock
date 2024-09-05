<?php
include './Config/Conexion.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segmentos_uri = explode('/', $uri);
$idPuja = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3]) ? $segmentos_uri[3] : null;
$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    try {
        if ($idPuja) {
            $consulta = $base_de_datos->prepare("SELECT * FROM puja WHERE idPuja = ?");
            $consulta->execute([$idPuja]);
            $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

            if ($resultado) {
                $respuesta = formatearRespuesta(true, "Puja encontrada exitosamente.", ['puja' => $resultado]);
            } else {
                $respuesta = formatearRespuesta(false, "No se encontró ninguna puja con el ID especificado.");
            }

        } else {
            $consulta = $base_de_datos->query("SELECT * FROM puja");
            $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
            $respuesta = formatearRespuesta(true, "Pujas obtenidas correctamente.", ['pujas' => $resultado]);
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
