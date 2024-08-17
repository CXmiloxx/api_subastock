<?php

    include './Config/Conexion.php';

    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segmentos_uri = explode('/', $uri);
    $idMedicamento = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3]) ? $segmentos_uri[3] : null;
    $metodo = $_SERVER['REQUEST_METHOD'];

    if ($metodo === 'GET') {
        try{
        if ($idMedicamento) {

            $consulta = $base_de_datos->prepare("SELECT nombre, dosis FROM medicamento WHERE idMedicamento = ?");
            $consulta->execute([$idMedicamento]);
            $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

            if ($resultado) {
                $respuesta = formatearRespuesta(true, "Medicamento encontrado exitosamente.", ['medicamento' => $resultado]);
            } else {
                $respuesta = formatearRespuesta(false, "No se encontró ningún Medicamento con el ID especificado.");
            }
        } else {
            $respuesta = formatearRespuesta(false, "ID del medicamento no especificado.");
        }

        } catch(PDOException $e) {
            $respuesta = formatearRespuesta(false, "Error en la consulta SQL: " . $e->getMessage());

        }
    }else{
        $respuesta = formatearRespuesta(false, "Método de solicitud no permitido. Se esperaba GET.");
    }

header('Content-Type: application/json');
echo json_encode($respuesta);
?>