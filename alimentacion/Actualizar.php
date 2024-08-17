<?php

include './Config/Conexion.php';

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo == 'PUT') {

    $contenido = trim(file_get_contents('php://input'));
    $datos = json_decode($contenido, true);

    if (!empty($datos['idAlimentacion']) && !empty($datos['tipo_alimento']) && !empty($datos['cantidad'])) {
        try {
            $alimentacionExiste = $base_de_datos->prepare("SELECT COUNT(*) FROM alimentacion WHERE idAlimentacion = ?");
            $alimentacionExiste->execute([$datos['idAlimentacion']]);
            $existe = $alimentacionExiste->fetchColumn();

            if ($existe) {
                $consulta = $base_de_datos->prepare("UPDATE alimentacion SET tipo_alimento = :tAli, cantidad = :can WHERE idAlimentacion = :idAli");
                $consulta->bindParam(':tAli', $datos['tipo_alimento']);
                $consulta->bindParam(':can', $datos['cantidad']);
                $consulta->bindParam(':idAli', $datos['idAlimentacion']);
                $proceso = $consulta->execute();

                if ($proceso) {
                    $respuesta = [
                        'status' => true,
                        'message' => "La alimentación se ha actualizado correctamente."
                    ];
                } else {
                    $respuesta = [
                        'status' => false,
                        'message' => "Hubo un error al intentar actualizar la alimentación."
                    ];
                }
            } else {
                $respuesta = [
                    'status' => false,
                    'message' => "La alimentación con el ID especificado no existe."
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
        'message' => "Método de solicitud no permitido. Se esperaba PUT."
    ];
}

header('Content-Type: application/json');
echo json_encode($respuesta);

?>
