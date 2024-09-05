<?php
    include './Config/Conexion.php';
    date_default_timezone_set('America/Bogota');

    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segmentos_uri = explode('/', $uri);
    $idEstado_Salud = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3]) ? $segmentos_uri[3] : null;
    $metodo = $_SERVER['REQUEST_METHOD'];

    if ($metodo === 'PUT') {
        if ($idEstado_Salud) {
            $contenido = trim(file_get_contents("php://input"));
            $datos = json_decode($contenido, true);

            if ( isset ( $datos['peso'], $datos['estado'] ) ) {
                $peso = $datos['peso'];
                $estado = $datos['estado'];
                $fecha_actual = date('Y-m-d H:i:s');

                try {
                    $consulta = $base_de_datos->prepare("UPDATE estado_salud SET peso = :pes, estado = :est, fecha = :fec WHERE idEstado_Salud = :idES");
                    $consulta->bindParam(':pes', $peso);
                    $consulta->bindParam(':est', $estado);
                    $consulta->bindParam(':fec', $fecha_actual);
                    $consulta->bindParam(':ides', $idEstado_Salud);
                    $proceso = $consulta->execute();

                    if ($proceso && $consulta->rowCount()) {
                        $respuesta = formatearRespuesta(true, "Estado de salud actualizado correctamente.");
                    } else {
                        $respuesta = formatearRespuesta(false, "No se pudo actualizar el estado de salud. Verifica los datos y vuelve a intentarlo.");                }
                
                    } catch (Exception $e) {
                        $respuesta = formatearRespuesta(false, "Error en la consulta SQL: " . $e->getMessage());
                }
            } else {
                $respuesta = formatearRespuesta(false, "Datos incompletos o inválidos. Asegúrate de enviar todos los campos requeridos.");
            }
        } else {
            $respuesta = formatearRespuesta(false, "Debe especificar un ID de usuario en la ruta.");
        }
    } else {
        $respuesta = formatearRespuesta(false, "Método de solicitud no permitido. Se esperaba PUT.");
    }

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    header("Content-Type: application/json");
    echo json_encode($respuesta);
?>
