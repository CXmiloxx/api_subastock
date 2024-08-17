<?php
    include './Config/Conexion.php';

    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segmentos_uri = explode('/', $uri);

    $idAlimentacion = null;

    if (isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3])) {
        $idAlimentacion = $segmentos_uri[3];
    }

    $metodo = $_SERVER['REQUEST_METHOD'];

    if ($metodo == 'DELETE') {
        if ($idAlimentacion) {
            try {
                $consulta = $base_de_datos->prepare("DELETE FROM alimentacion WHERE idAlimentacion = ?");
                $proceso = $consulta->execute([$idAlimentacion]);

                if ($proceso && $consulta->rowCount() != 0) {
                    $respuesta = [
                        'status' => true,
                        'message' => "La alimentación se ha eliminado correctamente."
                    ];
                } else {
                    $respuesta = [
                        'status' => false,
                        'message' => "Hubo un error al intentar eliminar la alimentación o no fue encontrada."
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
                'message' => "El ID proporcionado es inválido o está incompleto."
            ];
        }
        header('Content-Type: application/json');
        echo json_encode($respuesta);
    } else {
        $respuesta = [
            'status' => false,
            'message' => "Método de solicitud no permitido. Se esperaba DELETE."
        ];
        header('Content-Type: application/json');
        echo json_encode($respuesta);
    }
?>
