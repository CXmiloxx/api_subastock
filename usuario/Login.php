<?php

    include './Config/Conexion.php';

    $metodo = $_SERVER['REQUEST_METHOD'];

    if ($metodo === 'POST') {
        $contenido = trim(file_get_contents("php://input"));
        $datos = json_decode($contenido, true);

        if (!empty($datos['correo']) && !empty($datos['contraseña'])) {
            $correo = $datos['correo'];
            $contra = $datos['contraseña'];

            try {
                $consulta = $base_de_datos->prepare("SELECT idUsuario, contraseña FROM usuario WHERE correo = :correo");
                $consulta->bindParam(':correo', $correo);
                $consulta->execute();
                $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

                if ($usuario) {
                    if (password_verify($contra, $usuario['contraseña'])) {
                        $respuesta = formatearRespuesta(true, 'Login exitoso', ['idUsuario' => $usuario['idUsuario']]);

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

header('Content-Type: application/json');
echo json_encode($respuesta);

?>
