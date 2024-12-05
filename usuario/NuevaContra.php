<?php

include './Config/Conexion.php';
require __DIR__ . '/../vendor/autoload.php';

//descargue resend y puse la key necesaria 
$resend = Resend::client('re_jH2uWPrx_23dPhPG9FF6GweKQadcvejCr');

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'POST') {
    $contenido = trim(file_get_contents("php://input"));
    $data = json_decode($contenido, true);

    //desde el front mande la contraseña y solo actualize la base de datos 
    if (isset($data['correo'], $data['contraseña'])) {
        $correo = $data['correo'];
        $contrasena = $data['contraseña'];
        $contrasenaHash = password_hash($contrasena, PASSWORD_BCRYPT);

        try {
            $verificarCorreo = $base_de_datos->prepare("SELECT COUNT(*) AS total FROM usuario WHERE correo = :correo");
            $verificarCorreo->bindParam(':correo', $correo);
            $verificarCorreo->execute();
            $resultado = $verificarCorreo->fetch(PDO::FETCH_ASSOC);

            //validar primero si el correo existe para no tener errores
            if ($resultado['total'] > 0) {
                $consulta = $base_de_datos->prepare("UPDATE usuario SET contraseña = :con WHERE correo = :ema");
                $consulta->bindParam(':con', $contrasenaHash);
                $consulta->bindParam(':ema', $correo);
                $proceso = $consulta->execute();

                if ($proceso && $consulta->rowCount() > 0) {
                    try {
                        //estructura para enviar el correo desde resend esta en la documentacion de resend
                        $resendResponse = $resend->emails->send([
                            'from' => 'Camilo <onboarding@resend.dev>',
                            'to' => [$correo],
                            'subject' => 'Cambio de contraseña',
                            'text' => "Hola, se ha generado una nueva contraseña para tu cuenta: $contrasena. Por favor, inicia sesión y cámbiala lo antes posible."
                        ]);

                        if ($resendResponse) {
                            $respuesta = formatearRespuesta(true, "Cambio de contraseña exitoso. La contraseña estará en su correo.");
                        }
                    } catch (Exception $e) {
                        $respuesta = formatearRespuesta(false, "La contraseña se actualizó, pero ocurrió un error al enviar el correo: " . $e->getMessage());
                    }
                } else {
                    $respuesta = formatearRespuesta(false, "No se pudo cambiar la contraseña. Verifica los datos.");
                }
            } else {
                $respuesta = formatearRespuesta(false, "El correo proporcionado no existe en nuestra base de datos.");
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

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

echo json_encode($respuesta);

?>
