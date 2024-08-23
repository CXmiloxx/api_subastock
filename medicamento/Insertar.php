<?php

include './Config/Conexion.php';

    date_default_timezone_set('America/Bogota');
    $metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo == 'POST') {
    $contenido = trim(file_get_contents("php://input"));
    $datos = json_decode($contenido, true);

    if ( isset ( $datos['idAnimal'], $datos['nombre'], $datos['dosis'] ) ) {
        $idAnimal = $datos['idAnimal'];
        $nombre = $datos['nombre'];
        $dosis = $datos['dosis'];
        $fecha_actual = date('Y-m-d H:i:s');


        try {
            $consulta = $base_de_datos->prepare("INSERT INTO medicamento (idAnimal, nombre, dosis, fecha) VALUES(:idAn, :nom, :dos, :fec)");
            $consulta->bindParam(':idAn', $idAnimal);
            $consulta->bindParam(':nom', $nombre);
            $consulta->bindParam(':dos', $dosis);
            $consulta->bindParam(':fec', $fecha_actual);
            $proceso = $consulta->execute();

            if ($proceso) {
                $respuesta = formatearRespuesta(true, "El medicamento se ha insertado correctamente.");

            } else {
                $respuesta = formatearRespuesta(false, "Hubo un error al intentar insertar el medicamento.");
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


