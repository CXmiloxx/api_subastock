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

            if (!empty($datos['peso']) && !empty($datos['estado'])) {
                $peso = $datos['peso'];
                $estado = $datos['estado'];

                try {
                    $consulta = $base_de_datos->prepare("UPDATE estado_salud SET peso = :peso, estado = :estado, fecha = NOW() WHERE idEstado_Salud = :idEstado_Salud");
                    $consulta->bindParam(':peso', $peso);
                    $consulta->bindParam(':estado', $estado);
                    $consulta->bindParam(':idEstado_Salud', $idEstado_Salud);

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

header('Content-Type: application/json');
echo json_encode($respuesta);
?>
