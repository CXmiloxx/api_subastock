<?php

    include './Config/Conexion.php';

    date_default_timezone_set('America/Bogota');
    $metodo = $_SERVER['REQUEST_METHOD'];

    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segmentos_uri = explode('/', $uri);
    $idMedicamento = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3]) ? $segmentos_uri[3] : null;

    if ($metodo === 'PUT') {

        if($idMedicamento){
            $contenido = trim(file_get_contents("php://input"));
            $datos = json_decode($contenido, true);

            if (isset ( $datos['idMedicamento'], $datos['nombre'], $datos['dosis'] ) ) {
                $idMedicamento = $datos['idMedicamento'];
                $nombre = $datos['nombre'];
                $dosis = $datos['dosis'];
                $fecha_actual = date('Y-m-d H:i:s');
                
            try {
                $medicamentoExiste = $base_de_datos->prepare("SELECT COUNT(*) FROM medicamento WHERE idMedicamento = ?");
                $medicamentoExiste->execute([$idMedicamento]);

                if ( $medicamentoExiste->fetchColumn() ) {
                    $consulta = $base_de_datos->prepare("UPDATE medicamento SET nombre = :nom, dosis = :dos, fecha = :fec WHERE idMedicamento = :idM");
                    $consulta->bindParam(':idM', $idMedicamento);
                    $consulta->bindParam(':nom', $nombre);
                    $consulta->bindParam(':dos', $dosis);
                    $consulta->bindParam(':fec', $fecha_actual);
                    $proceso = $consulta->execute();

                    if ($proceso && $consulta->rowCount() ) {
                        $respuesta = formatearRespuesta(true, "Medicamento actualizado correctamente.");

                    } else {
                        $respuesta = formatearRespuesta(false, "No se pudo actualizar el medicamento. Verifica los datos y vuelve a intentarlo.");
                    }

                    } else {
                        $respuesta = formatearRespuesta(false, "El medicamento con el ID especificado no existe.");
                    }
                } catch (Exception $e) {
                    $respuesta = formatearRespuesta(false, "Error en la consulta SQL: " . $e->getMessage());
                }
            } else {
                $respuesta = formatearRespuesta(false, "Datos incompletos o inválidos. Asegúrate de enviar todos los campos requeridos.");
            }

        } else {
            $respuesta = formatearRespuesta(false, "Debe especificar un ID de usuario en la ruta.");
        }
    
    }else{
        $respuesta = formatearRespuesta(false, "Método de solicitud no permitido. Se esperaba PUT.");
    }

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    header("Content-Type: application/json");
    echo json_encode($respuesta);

?>
