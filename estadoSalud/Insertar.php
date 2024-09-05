<?php

    include './Config/Conexion.php';
    date_default_timezone_set('America/Bogota');

    $metodo = $_SERVER['REQUEST_METHOD'];

    if ($metodo == 'POST') {
        $contenido = trim(file_get_contents("php://input"));
        $datos = json_decode($contenido, true);

        if ( isset ( $datos['idAnimal'], $datos['peso'], $datos['estado'] ) ) {
            $idAnimal = $datos['idAnimal'];
            $peso = $datos['peso'];
            $estado = $datos['estado'];
            $fecha_actual = date('Y-m-d H:i:s');


            try {
                $consulta = $base_de_datos->prepare("INSERT INTO estado_salud (idAnimal, peso, estado, fecha) VALUES(:idAn, :pes, :est, :fec)");
                $consulta->bindParam(':idAn', $idAnimal);
                $consulta->bindParam(':pes', $peso);
                $consulta->bindParam(':est', $estado);
                $consulta->bindParam(':fec', $fecha_actual);
                $proceso = $consulta->execute();

                if ($proceso) {
                    $respuesta = [
                        'status' => true,
                        'message' => "El estado de salud se ha insertado correctamente."
                    ];
                } else {
                    $respuesta = [
                        'status' => false,
                        'message' => "Hubo un error al intentar insertar el estado de salud "
                    ];
                }
            } catch (Exception $e) {
                $respuesta = [
                    'status' => false,
                    'message' => "Error en la consulta SQL.",
                    'exception' => $e->getMessage()
                ];
            }
        } else {
            $respuesta = [
                'status' => false,
                'message' => "Los datos enviados en la solicitud son inválidos o incompletos."
            ];
        }
    } else {
        $respuesta = [
            'status' => false,
            'message' => "Método de solicitud no permitido. Se esperaba POST."
        ];
    }

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    header("Content-Type: application/json");
    echo json_encode($respuesta);
?>


