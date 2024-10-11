<?php

include './Config/Conexion.php';

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo == 'POST') {
    $contenido = trim(file_get_contents("php://input"));
    $datos = json_decode($contenido, true);

    if (isset($datos['idUsuario'], $datos['marca'], $datos['raza'], $datos['especie'])) {
        $idUsuario = $datos['idUsuario'];
        $marca = $datos['marca'];
        $raza = $datos['raza'];
        $especie = $datos['especie'];

        try {
            $marcaExistente = $base_de_datos->prepare('
                SELECT COUNT(*)
                FROM animal
                WHERE marca = :mar AND especie = :esp
            ');
            $marcaExistente->bindParam(':mar', $marca);
            $marcaExistente->bindParam(':esp', $especie);
            $marcaExistente->execute();

            if ($marcaExistente->fetchColumn() > 0) {
                $respuesta = formatearRespuesta(false, "La marca de este animal ya existe en la misma especie.");
            } else {
                try {
                    $consulta = $base_de_datos->prepare("
                        INSERT INTO animal (idUsuario, marca, raza, especie) 
                        VALUES (:idU, :mar, :raza, :esp)
                    ");
                    $consulta->bindParam(':idU', $idUsuario);
                    $consulta->bindParam(':mar', $marca);
                    $consulta->bindParam(':raza', $raza);
                    $consulta->bindParam(':esp', $especie);

                    if ($consulta->execute()) {
                        $respuesta = formatearRespuesta(true, "El animal se ha insertado correctamente.");
                    } else {
                        $respuesta = formatearRespuesta(false, "Hubo un error al intentar insertar el animal.");
                    }
                } catch (Exception $e) {
                    $respuesta = formatearRespuesta(false, 'Error al intentar registrar el animal: ' . $e->getMessage());
                }
            }
        } catch (ErrorException $e) {
            $respuesta = formatearRespuesta(false, 'Error al intentar verificar la marca y especie: ' . $e->getMessage());
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
