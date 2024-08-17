<?php

    include './Config/Conexion.php';
    date_default_timezone_set('America/Bogota');

    $metodo = $_SERVER['REQUEST_METHOD'];

    if ($metodo == 'POST') {
        $contenido = trim(file_get_contents("php://input"));
        $datos = json_decode($contenido, true);

        if (!empty($datos['idAnimal']) && !empty($datos['peso']) && !empty($datos['estado'])) {
            $idAnimal = $datos['idAnimal'];
            $peso = $datos['peso'];
            $estado = $datos['estado'];


            try {
                $consulta = $base_de_datos->prepare("INSERT INTO estado_salud (idAnimal, peso, estado, fecha) VALUES(:idAn, :pes, :est, NOW() )");
                $consulta->bindParam(':idAn', $idAnimal);
                $consulta->bindParam(':pes', $peso);
                $consulta->bindParam(':est', $estado);
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

header('Content-Type: application/json');
echo json_encode($respuesta);
?>


