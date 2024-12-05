<?php
// cambiarContraseña.php
include '../Config/Conexion.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $email = isset($data['email']) ? $data['email'] : null;
    $contraseñaActual = isset($data['contraseñaActual']) ? $data['contraseñaActual'] : null;
    $nuevaContraseña = isset($data['nuevaContraseña']) ? $data['nuevaContraseña'] : null;

    if ($email && $contraseñaActual && $nuevaContraseña) {
        try {
            $consulta = $base_de_datos->prepare("SELECT * FROM usuarios WHERE email = ?");
            $consulta->execute([$email]);
            $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                if (password_verify($contraseñaActual, $usuario['contraseña'])) {
                    $consultaActualizar = $base_de_datos->prepare("UPDATE usuarios SET contraseña = ? WHERE email = ?");
                    $consultaActualizar->execute([password_hash($nuevaContraseña, PASSWORD_DEFAULT), $email]);

                    $respuesta = formatearRespuesta(true, "Contraseña actualizada correctamente.");
                } else {
                    $respuesta = formatearRespuesta(false, "La contraseña actual es incorrecta.");
                }
            } else {
                $respuesta = formatearRespuesta(false, "Usuario no encontrado.");
            }
        } catch (Exception $e) {
            $respuesta = formatearRespuesta(false, "Error en la consulta SQL: " . $e->getMessage());
        }
    } else {
        $respuesta = formatearRespuesta(false, "Todos los campos son obligatorios.");
    }
} else {
    $respuesta = formatearRespuesta(false, "Método no permitido. Se esperaba POST.");
}

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true"); // Permite el uso de cookies/sesiones
header("Access-Control-Allow-Origin: http://localhost:5173"); // Especifica la URL exacta del frontend

echo json_encode($respuesta);

?>
