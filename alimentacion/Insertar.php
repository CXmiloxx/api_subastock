<?php
include './Config/Conexion.php';

    date_default_timezone_set('America/Bogota');

    $metodo = $_SERVER['REQUEST_METHOD'];

    if ($metodo == 'POST') {
        $contenido = trim(file_get_contents("php://input"));
        $datos = json_decode($contenido, true);

        if (!empty($datos['idAnimal']) && !empty($datos['tipo_alimento']) && !empty($datos['cantidad'])) {
            $idAnimal = $datos['idAnimal'];
            $tipo_alimento = $datos['tipo_alimento'];
            $cantidad = $datos['cantidad'];
        
            try {
                $consulta = $base_de_datos->prepare("INSERT INTO alimentacion(idAnimal,tipo_alimento,cantidad,fecha) VALUES(:idAn, :tipA, :can, NOW())");
                $consulta->bindParam(':idAn', $idAnimal);
                $consulta->bindParam(':tipA', $tipo_alimento);
                $consulta->bindParam(':can', $cantidad);
            

            if ($consulta->execute()) {
                $respuesta = formatearRespuesta(true, "El Alimento se ha insertado correctamente.");
            } else {
                $respuesta = formatearRespuesta(false, "Hubo un error al intentar insertar el alimento.");
            }
        } catch (Exception $e) {
            $respuesta = formatearRespuesta(false, "Error en la consulta SQL: ". $e->getMessage());
        }
    } else {
        $respuesta = formatearRespuesta(false, "Datos incompletos o inválidos. Asegúrate de enviar todos los campos requeridos.");
    }
} else {
    $respuesta = formatearRespuesta(false, "Método de solicitud no permitido. Se esperaba POST.");
}

header('Content-Type: application/json');
echo json_encode($respuesta);
?>
