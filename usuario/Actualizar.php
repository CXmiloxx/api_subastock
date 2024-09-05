<?php
    include './Config/Conexion.php';

    $metodo = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segmentos_uri = explode('/', $uri);
    $idUsuario = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3]) ? $segmentos_uri[3] : null;

    if ($metodo === 'PUT') {
        
        if ($idUsuario) {
            $contenido = trim(file_get_contents("php://input"));
            $datos = json_decode($contenido, true);

            if (isset($datos['nombres'],$datos['apellidos'],$datos['correo'],$datos['contraseña'],$datos['saldo'],$datos['telefono'])) {
                $nombre = $datos['nombres'];
                $apellido = $datos['apellidos'];
                $correo = $datos['correo'];
                $contra = password_hash($datos['contraseña'], PASSWORD_BCRYPT);
                $saldo = $datos['saldo'];
                $telefono = $datos['telefono'];

                try {
                    $usuarioExiste = $base_de_datos->prepare("SELECT COUNT(*) FROM usuario WHERE idUsuario = ?");
                    $usuarioExiste->execute([$idUsuario]);

                    if ($usuarioExiste->fetchColumn()) {
                        $consulta = $base_de_datos->prepare("UPDATE usuario SET nombres = :nom, apellidos = :ape, correo = :cor, contraseña = :cont, saldo = :sal, telefono = :tel WHERE idUsuario = :idU");
                        $consulta->bindParam(':idU', $idUsuario);
                        $consulta->bindParam(':nom', $nombre);
                        $consulta->bindParam(':ape', $apellido);
                        $consulta->bindParam(':cor', $correo);
                        $consulta->bindParam(':cont', $contra);
                        $consulta->bindParam(':sal', $saldo);
                        $consulta->bindParam(':tel', $telefono);
                        $proceso = $consulta->execute();

                        if ($proceso && $consulta->rowCount()) {
                            $respuesta = formatearRespuesta(true, "Usuario actualizado correctamente.");
                        } else {
                            $respuesta = formatearRespuesta(false, "No se pudo actualizar el usuario. Verifica los datos y vuelve a intentarlo.");
                        }
                    } else {
                        $respuesta = formatearRespuesta(false, "Usuario con el ID especificado no existe.");
                    }

                } catch (Exception $e) {
                    $respuesta = formatearRespuesta(false, "Error en la consulta SQL: " . $e->getMessage());
                }
            } else {
                $respuesta = formatearRespuesta(false, "Datos incompletos o inválidos. Asegúrate de enviar todos los campos requeridos.");
            }
        } else {
            $respuesta = formatearRespuesta(false, "Debe especificar un ID de usuario en la ruta.");
        }
    } else {
        $respuesta = formatearRespuesta(false, "Método de solicitud no permitido. Se esperaba PUT.");
    }

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    header("Content-Type: application/json");
    echo json_encode($respuesta);
?>
