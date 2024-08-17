<?php

    include './Config/Conexion.php';

    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segmentos_uri = explode('/', $uri);
    $idEstado_Salud = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3]) ? $segmentos_uri[3] : null;
    $metodo = $_SERVER['REQUEST_METHOD'];


    if ($metodo === 'GET') {
        try {
            if ($idEstado_Salud) {

                $consulta = $base_de_datos->prepare("SELECT idEstado_Salud, peso,estado FROM estado_salud WHERE idEstado_Salud = ?");
                $consulta->execute([$idEstado_Salud]);
                $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

                if ($resultado) {
                    $respuesta = formatearRespuesta(true, "Estados de Salud obtenidos correctamente.", ['Estado_Salud' => $resultado]);
                } else {
                    $respuesta = formatearRespuesta(false, "No se encontraron estados de salud para el ID de animal especificado.");
                }
            } else {
                $respuesta = formatearRespuesta(false, "ID de estado de salud no especificado.");
            }
        } catch (Exception $e) {
            $respuesta = formatearRespuesta(false, "Error en la consulta SQL: " . $e->getMessage());
        }
    } else {
        $respuesta = formatearRespuesta(false, "Método de solicitud no permitido. Se esperaba GET.");
    }

header('Content-Type: application/json');
echo json_encode($respuesta);

?>
