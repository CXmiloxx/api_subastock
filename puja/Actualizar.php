<?php

    include "./Config/Conexion.php";

    $metodo = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segmentos_uri = explode('/', $uri);
    $idPuja = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3]) ? $segmentos_uri[3] : null;

    if ($metodo === 'PUT') {
        if ($idPuja) {
            $contenido = trim(file_get_contents("php://input"));
            $datos = json_decode($contenido, true);

            if (!empty($datos['valor'])) {
                $valor = $datos['valor'];

                try {
                    $pujaExiste = $base_de_datos->prepare("SELECT COUNT(*) FROM puja WHERE idPuja = ?");
                    $pujaExiste->execute([$idPuja]);

                    if ($pujaExiste->fetchColumn()) {
                        $consulta = $base_de_datos->prepare("UPDATE puja SET valor = :valor WHERE idPuja = :idPuja");
                        $consulta->bindParam(':idPuja', $idPuja, PDO::PARAM_INT);
                        $consulta->bindParam(':valor', $valor, PDO::PARAM_STR);
                        $proceso = $consulta->execute();

                        if ($proceso && $consulta->rowCount() > 0) {
                            $respuesta = formatearRespuesta(true, "Puja actualizada correctamente.");
                        } else {
                            $respuesta = formatearRespuesta(false, "No se pudo actualizar la puja. Verifica los datos y vuelve a intentarlo.");
                        }
                    } else {
                        $respuesta = formatearRespuesta(false, "La puja con el ID especificado no existe.");
                    }
                } catch (Exception $e) {
                    $respuesta = formatearRespuesta(false, "Error en la consulta SQL: " . $e->getMessage());
                }
            } else {
                $respuesta = formatearRespuesta(false, "Datos incompletos o inválidos. Asegúrate de enviar todos los campos requeridos.");
            }
        } else {
            $respuesta = formatearRespuesta(false, "Debe especificar un ID de puja en la ruta.");
        }
    } else {
        $respuesta = formatearRespuesta(false, "Método de solicitud no permitido. Se esperaba PUT.");
    }

header('Content-Type: application/json');
echo json_encode($respuesta);

?>
