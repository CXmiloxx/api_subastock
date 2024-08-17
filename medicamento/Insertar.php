<?php

include './Config/Conexion.php';

    date_default_timezone_set('America/Bogota');
    $metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo == 'POST') {
    $contenido = trim(file_get_contents("php://input"));
    $datos = json_decode($contenido, true);

    if (!empty($datos['idAnimal']) && !empty($datos['nombre']) && !empty($datos['dosis'])) {
        $idAnimal = $datos['idAnimal'];
        $nombre = $datos['nombre'];
        $dosis = $datos['dosis'];


        try {
            $consulta = $base_de_datos->prepare("INSERT INTO medicamento(idAnimal, nombre, dosis, fecha) VALUES(:idAn, :nom, :dos, NOW())");
            $consulta->bindParam(':idAn', $idAnimal);
            $consulta->bindParam(':nom', $nombre);
            $consulta->bindParam(':dos', $dosis);
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


