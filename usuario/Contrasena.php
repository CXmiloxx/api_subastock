<?php

include './Config/Conexion.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'POST') {
    $contenido = trim(file_get_contents("php://input"));
    $data = json_decode($contenido, true);

    // Verificar que se reciban los datos necesarios
    if (isset($data['correo'], $data['contraseña'])) {
        $correo = $data['correo'];
        $contrasena = $data['contraseña'];
        $contrasenaHash = password_hash($contrasena, PASSWORD_BCRYPT);

        try {
            // Actualizar la contraseña para el correo especificado
            $consulta = $base_de_datos->prepare("UPDATE usuario SET contraseña = :con WHERE correo = :ema");
            $consulta->bindParam(':con', $contrasenaHash);
            $consulta->bindParam(':ema', $correo);
            $proceso = $consulta->execute();

            if ($proceso && $consulta->rowCount() > 0) {
                $respuesta = formatearRespuesta(true, "Contraseña actualizada correctamente.");
            } else {
                $respuesta = formatearRespuesta(false, "No se pudo actualizar la contraseña. Verifica los datos.");
            }
        } catch (Exception $e) {
            $respuesta = formatearRespuesta(false, "Error en la consulta SQL: " . $e->getMessage());
        }
    } else {
        $respuesta = formatearRespuesta(false, "Datos incompletos o inválidos.");
    }
} else {
    $respuesta = formatearRespuesta(false, "Método de solicitud no permitido. Se esperaba POST.");
}

// Configuración de cabeceras
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

echo json_encode($respuesta);

?>
