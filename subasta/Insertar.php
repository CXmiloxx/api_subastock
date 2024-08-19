<?php
include './Config/Conexion.php';

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo == 'POST') {
    $contenido = trim(file_get_contents("php://input"));
    $datos = json_decode($contenido, true);

    if (isset($datos['idUsuario'], $datos['idAnimal'], $datos['pujaMinima'], $datos['fechaInicio'], $datos['fechaFin'])) {
        $idUsuario = $datos['idUsuario'];
        $idAnimal = $datos['idAnimal'];
        $pujaMinima = $datos['pujaMinima'];
        $fechaInicio = $datos['fechaInicio'];
        $fechaFin = $datos['fechaFin'];

        try {
            $consulta = $base_de_datos->prepare("INSERT INTO subasta (idUsuario, idAnimal, pujaMinima, fechaInicio, fechaFin) VALUES (:idU, :idA, :pMin, :fIni, :fFin)");
            $consulta->bindParam(':idU', $idUsuario);
            $consulta->bindParam(':idA', $idAnimal);
            $consulta->bindParam(':pMin', $pujaMinima);
            $consulta->bindParam(':fIni', $fechaInicio);
            $consulta->bindParam(':fFin', $fechaFin);
            $proceso = $consulta->execute();

            if ($proceso) {
                $respuesta = formatearRespuesta(true, "Subasta insertada correctamente.");
            } else {
                $respuesta = formatearRespuesta(false, "No se pudo insertar la subasta. Verifica los datos y vuelve a intentarlo.");
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

header('Content-Type: application/json');
echo json_encode($respuesta);
?>
