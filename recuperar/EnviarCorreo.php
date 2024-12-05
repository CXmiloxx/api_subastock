<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Verifica que esta ruta sea válida.
include '../Config/Conexion.php'; // Incluye la conexión a la base de datos.

// Habilitar CORS
header('Access-Control-Allow-Origin: http://localhost:5173'); // Cambia a la URL de tu frontend en producción
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');  // Métodos permitidos
header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Headers permitidos

// Manejo de solicitudes preflight (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); // Respuesta exitosa para el preflight
    exit(); // Detén la ejecución para solicitudes OPTIONS
}

// Leer y decodificar el JSON enviado desde el cliente.
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Validar que el cuerpo de la solicitud sea un JSON válido.
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['status' => false, 'message' => 'El cuerpo de la solicitud debe ser un JSON válido.']);
    http_response_code(400);
    exit();
}

// Validar que el campo 'email' esté presente y no esté vacío.
if (!isset($data['email']) || empty($data['email'])) {
    echo json_encode(['status' => false, 'message' => 'El campo correo es obligatorio.']);
    http_response_code(400);
    exit();
}

$email = $data['email'];

try {
    // Verificar si el correo existe en la base de datos.
    $consulta = $base_de_datos->prepare("SELECT * FROM usuarios WHERE email = ?");
    $consulta->execute([$email]);
    $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        // Si el correo no está registrado.
        echo json_encode(['status' => false, 'message' => 'El correo proporcionado no está registrado.']);
        http_response_code(404);
        exit();
    }

    // Generar una nueva contraseña aleatoria.
    $nuevaContraseña = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 8);

    // Actualizar la contraseña en la base de datos (usando hash para mayor seguridad).
    $consultaActualizar = $base_de_datos->prepare("UPDATE usuarios SET contraseña = ? WHERE email = ?");
    $consultaActualizar->execute([password_hash($nuevaContraseña, PASSWORD_DEFAULT), $email]);

    // Configuración y envío del correo con PHPMailer.
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP.
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Servidor SMTP de Gmail.
        $mail->SMTPAuth = true;
        $mail->Username = 'jaramillox15@gmail.com'; // Tu correo de Gmail.
        $mail->Password = 'ocjg xhij zxsy mltk'; // Usa la contraseña de aplicación.
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Destinatarios.
        $mail->setFrom('x336562@gmail.com', 'Soporte');
        $mail->addAddress($email); // Correo del destinatario.

        // Contenido del correo.
        $mail->isHTML(true);
        $mail->Subject = 'Recuperación de Contraseña';
        $mail->Body = "
            <h1>Recuperación de Contraseña</h1>
            <p>Hola, {$usuario['nombre']},</p>
            <p>Tu nueva contraseña temporal es: <strong>$nuevaContraseña</strong></p>
            <p>Te recomendamos cambiarla después de iniciar sesión.</p>
        ";

        // Enviar el correo.
        $mail->send();

        // Respuesta exitosa.
        echo json_encode(['status' => true, 'message' => 'Correo enviado correctamente.']);
    } catch (Exception $e) {
        // Error al enviar el correo.
        echo json_encode(['status' => false, 'message' => "Error al enviar el correo: {$mail->ErrorInfo}"]);
        http_response_code(500);
        exit();
    }
} catch (Exception $e) {
    // Error general en el backend.
    echo json_encode(['status' => false, 'message' => "Error en el servidor: {$e->getMessage()}"]);
    http_response_code(500);
    exit();
}
