<?php
    include './Config/Conexion.php';

    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segmentos_uri = explode('/', $uri);
    $idUsuario = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3]) ? $segmentos_uri[3] : null;
    $metodo = $_SERVER['REQUEST_METHOD'];

    if ($metodo === 'GET') {
        try {
            if ($idUsuario) {
                $consulta = $base_de_datos->prepare("SELECT idUsuario, nombres, telefono, contraseña FROM usuario WHERE idUsuario = ?");
                $consulta->execute([$idUsuario]);
                $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
                
                if ($resultado) {
                    $respuesta = formatearRespuesta(true, "Usuario encontrado exitosamente.", ['usuario' => $resultado]);

                } else {
                    $respuesta = formatearRespuesta(false, "No se encontró ningún usuario con el ID especificado.");
                }

            } else {
                $respuesta = formatearRespuesta(false, "ID del Usuario no especificado.");
            }

        } catch (Exception $e) {
            $respuesta = formatearRespuesta(false, "Error en la consulta SQL: " . $e->getMessage());
        }

    } else {
        $respuesta = formatearRespuesta(false, "Método de solicitud no permitido. Se esperaba GET.");
    }

header('Content-Type: application/json');
echo json_encode($respuesta);
?>
