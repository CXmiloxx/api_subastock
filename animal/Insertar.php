<?php

    include './Config/Conexion.php';

    $metodo = $_SERVER['REQUEST_METHOD'];

    if ($metodo == 'POST') {
        $contenido = trim(file_get_contents("php://input"));
        $datos = json_decode($contenido, true);

        if (isset ( $datos['idUsuario'], $datos['marca'], $datos['raza'], $datos['especie'] ) ) {
            $idUsuario = $datos['idUsuario'];
            $marca = $datos['marca'];
            $raza = $datos['raza'];
            $especie = $datos['especie'];

            try {
                $consulta = $base_de_datos->prepare("INSERT INTO animal (idUsuario, marca, raza, especie) VALUES (:idU, :mar, :raza, :esp)");
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