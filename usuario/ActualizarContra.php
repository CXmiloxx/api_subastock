<?php

include './Config/Conexion.php';

// Configuración CORS para aceptar solicitudes desde el frontend
header("Access-Control-Allow-Origin: http://localhost:5173"); // Cambia el dominio si es necesario
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

// Manejo de preflight (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204); // Sin contenido
    exit();
}

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'POST') {
    $contenido = trim(file_get_contents("php://input"));
    $data = json_decode($contenido, true);

    // Validar datos enviados desde el frontend
    if (isset($data['contraseñaActual'], $data['nuevaContraseña'], $data['correo'])) {
        $correo = $data['correo'];
        $contraseñaActual = $data['contraseñaActual'];
        $nuevaContraseña = $data['nuevaContraseña'];
        $nuevaContraseñaHash = password_hash($nuevaContraseña, PASSWORD_BCRYPT);

        try {
            // Verificar la contraseña actual en la base de datos
            $consulta = $base_de_datos->prepare("SELECT contraseña FROM usuario WHERE correo = :correo");
            $consulta->bindParam(':correo', $correo);
            $consulta->execute();
            $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($contraseñaActual, $usuario['contraseña'])) {
                // Actualizar la contraseña
                $actualizar = $base_de_datos->prepare("UPDATE usuario SET contraseña = :nueva WHERE correo = :correo");
                $actualizar->bindParam(':nueva', $nuevaContraseñaHash);
                $actualizar->bindParam(':correo', $correo);
                $actualizar->execute();

                if ($actualizar->rowCount() > 0) {
                    echo json_encode(["success" => true, "message" => "Contraseña actualizada correctamente."]);
                } else {
                    echo json_encode(["success" => false, "message" => "No se pudo actualizar la contraseña."]);
                }
            } else {
                echo json_encode(["success" => false, "message" => "La contraseña actual no es correcta."]);
            }
        } catch (Exception $e) {
            echo json_encode(["success" => false, "message" => "Error en el servidor: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Datos incompletos o inválidos."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Método no permitido."]);
}

?>
