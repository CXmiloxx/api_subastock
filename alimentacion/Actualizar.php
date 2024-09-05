<?php

    include './Config/Conexion.php';
    $metodo = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segmentos_uri = explode('/', $uri);
    $idAlimentacion = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3]) ? $segmentos_uri[3] : null;


    if ($metodo == 'PUT') {

        if ($idAlimentacion) {
            $contenido = trim(file_get_contents('php://input'));
            $datos = json_decode($contenido, true);

            if (isset($datos['tipo_alimento'], $datos['cantidad'])) {
                $tipo_alimento = $datos['tipo_alimento'];
                $cantidad = $datos['cantidad'];

                try {
                    $alimentacionExiste = $base_de_datos->prepare("SELECT COUNT(*) FROM alimentacion WHERE idAlimentacion = ?");
                    $alimentacionExiste->execute([$idAlimentacion]);

                    if ($alimentacionExiste-> fetchColumn()) {
                        $consulta = $base_de_datos->prepare("UPDATE alimentacion SET tipo_alimento = :tAli, cantidad = :can WHERE idAlimentacion = :idAli");
                        $consulta->bindParam(':tAli', $tipo_alimento);
                        $consulta->bindParam(':can', $cantidad);
                        $proceso = $consulta->execute();

                        if ($proceso && $consulta->rowCount()) {
                            $respuesta = formatearRespuesta(true, "Alimentacion actualizado correctamente.");
                        } else {
                            $respuesta = formatearRespuesta(false, "No se pudo actualizar la alimentacion del animal. Verifica los datos y vuelve a intentarlo.");
                        }
                    } else {
                        $respuesta = formatearRespuesta(false, "La alimentacion con el ID especificado no existe.");
                    }
                } catch (Exception $e) {
                    $respuesta = formatearRespuesta(false, "Error en la consulta SQL: ". $e->getMessage());
                }
            } else {
                $respuesta = formatearRespuesta(false, "Datos incompletos o inválidos. Asegúrate de enviar todos los campos requeridos.");
            }
        } else {
            $respuesta = formatearRespuesta(false, "Debe especificar un ID de usuario en la ruta.");
        }
    }

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    header("Content-Type: application/json");
        echo json_encode($respuesta);
?>