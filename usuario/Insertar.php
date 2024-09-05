<?php
include './Config/Conexion.php';

    $metodo = $_SERVER['REQUEST_METHOD'];

    if ($metodo === 'POST') {
        $contenido = trim(file_get_contents("php://input"));
        $datos = json_decode($contenido, true);

        if (isset($datos['nombres'], $datos['apellidos'], $datos['correo'], $datos['contraseña'], $datos['saldo'], $datos['telefono'])) {
            $nombre = $datos['nombres'];
            $apellido = $datos['apellidos'];
            $correo = $datos['correo'];
            $contra = password_hash($datos['contraseña'], PASSWORD_BCRYPT);
            $saldo = $datos['saldo'];
            $telefono = $datos['telefono'];

            try {
                $consultaCorreo = $base_de_datos->prepare("SELECT COUNT(*) FROM usuario WHERE correo = :cor");
                $consultaCorreo->bindParam(':cor', $correo);
                $consultaCorreo->execute();

                if ($consultaCorreo->fetchColumn() > 0) {
                    $respuesta = formatearRespuesta(false, 'El correo ya existe. Registrese con otro correo');
                    
                } else {
                    $consulta = $base_de_datos->prepare("INSERT INTO usuario (nombres, apellidos, correo, contraseña, saldo, telefono) VALUES (:nom, :ape, :cor, :cont, :sal, :tel)");
                    $consulta->bindParam(':nom', $nombre);
                    $consulta->bindParam(':ape', $apellido);
                    $consulta->bindParam(':cor', $correo);
                    $consulta->bindParam(':cont', $contra);
                    $consulta->bindParam(':sal', $saldo);
                    $consulta->bindParam(':tel', $telefono);

                    if ($consulta->execute()) {
                        $respuesta = formatearRespuesta(true, "El usuario se ha insertado correctamente.");
                    } else {
                        $respuesta = formatearRespuesta(false, "No se pudo insertar el usuario. Verifica los datos y vuelve a intentarlo.");
                    }
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

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    header("Content-Type: application/json");echo json_encode($respuesta);
?>
