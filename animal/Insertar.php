<?php

    include './Config/Conexion.php';

    $metodo = $_SERVER['REQUEST_METHOD'];

    if ($metodo == 'POST') {
        $content = trim(file_get_contents("php://input"));
        $decoded = json_decode($content, true);

        if (!empty($decoded['idUsuario']) && !empty($decoded['marca']) && !empty($decoded['raza']) && !empty($decoded['especie'])) {
            $idUsuario = $decoded['idUsuario'];
            $marca = $decoded['marca'];
            $raza = $decoded['raza'];
            $especie = $decoded['especie'];

            try {
                $consulta = $base_de_datos->prepare("INSERT INTO Animal (idUsuario, marca, raza, especie) VALUES (:idU, :mar, :raza, :esp)");
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
                $respuesta = formatearRespuesta(false, 'Error al intentar registrar el usuario: ' . $e->getMessage());
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