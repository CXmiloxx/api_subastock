<?php
include './Config/Conexion.php';

date_default_timezone_set('America/Bogota');

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo == 'POST') {
    $contenido = trim(file_get_contents("php://input"));
    $datos = json_decode($contenido, true);

    if (isset($datos['idAnimal'], $datos['tipo_alimento'], $datos['cantidad'])) {
        $idAnimal = $datos['idAnimal'];
        $tipo_alimento = $datos['tipo_alimento'];
        $cantidad = $datos['cantidad'];
        $fecha_actual = date('Y-m-d H:i:s');
        
        try {
            $consulta = $base_de_datos->prepare("INSERT INTO alimentacion(idAnimal, tipo_alimento, cantidad, fecha) VALUES(:idAn, :tipA, :can, :fecha)");
            $consulta->bindParam(':idAn', $idAnimal);
            $consulta->bindParam(':tipA', $tipo_alimento);
            $consulta->bindParam(':can', $cantidad);
            $consulta->bindParam(':fecha', $fecha_actual);

            if ($consulta->execute()) {
                $respuesta = formatearRespuesta(true, "El Alimento se ha insertado correctamente.");
            } else {
                $respuesta = formatearRespuesta(false, "Hubo un error al intentar insertar el alimento.");
            }
        } catch (Exception $e) {
            $respuesta = formatearRespuesta(false, "Error en la consulta SQL: " . $e->getMessage());
        }
    } else {
        $respuesta = formatearRespuesta(false, "Datos incompletos o inválidos. Asegúrate de enviar todos los campos requeridos.");
    }
} else {
    $respuesta = formatearRespuesta(false, "Método de solicitud no permitido. Se esperaba POST.");
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");
echo json_encode($respuesta);
?>
