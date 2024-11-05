<?php

    include './Config/Conexion.php';
    include './Config/Token.php';

    $metodo = $_SERVER['REQUEST_METHOD'];

    if ($metodo === 'POST') {
        $contenido = trim(file_get_contents("php://input"));
        $datos = json_decode($contenido, true);

        if (isset($datos['correo'], $datos['contraseña'])) {
            $correo = $datos['correo'];
            $contra = $datos['contraseña'];

            try {
                $consulta = $base_de_datos->prepare("SELECT idUsuario, contraseña FROM usuario WHERE correo = :correo");
                $consulta->bindParam(':correo', $correo);
                $consulta->execute();
                $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

                if ($usuario) {
                    if (password_verify($contra, $usuario['contraseña'])) {
                        $token = Token::createToken($correo, $usuario['idUsuario']);
                        $respuesta = formatearRespuesta(true, 'Login exitoso', ["token" => $token]);

                    } else {
                        $respuesta = formatearRespuesta(false, 'Contraseña incorrecta');
                    }

                } else {
                    $respuesta = formatearRespuesta(false, 'Correo incorrecto');
                }
            } catch (Exception $e) {
                $respuesta = formatearRespuesta(false, 'Error al intentar iniciar sesión: ' . $e->getMessage());
            }

        } else {
            $respuesta = formatearRespuesta(false, 'Correo y contraseña son requeridos');
        }

    } else {
        $respuesta = formatearRespuesta(false, 'Método no permitido, se esperaba POST');
    }

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    header("Content-Type: application/json");
echo json_encode($respuesta);

?>
