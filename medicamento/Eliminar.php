<?php

    include './Config/Conexion.php';

    // Obtener la URI y segmentarla
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segmentos_uri = explode('/', $uri);

    $idMedicamento = null;

    if (isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3])) {
        $idMedicamento = $segmentos_uri[3];
    }

    $metodo = $_SERVER['REQUEST_METHOD'];

    if ($metodo === 'DELETE') {
        if ($idMedicamento) {
            try {
                $consulta = $base_de_datos->prepare("DELETE FROM medicamento WHERE idMedicamento = ?");
                $proceso = $consulta->execute([$idMedicamento]);

                if ($proceso && $consulta->rowCount() != 0) {
                    $respuesta = [
                        'status' => true,
                        'message' => "El medicamento se ha eliminado correctamente."
                    ];
                } else {
                    $respuesta = [
                        'status' => false,
                        'message' => "Hubo un error al intentar eliminar el medicamento o no fue encontrado."
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
                'message' => "Debe especificar un id de medicamento."
            ];
        }
    } else {
        $respuesta = [
            'status' => false,
            'message' => "Método de solicitud no permitido. Se esperaba DELETE."
        ];
    }

header('Content-Type: application/json');
echo json_encode($respuesta);

?>