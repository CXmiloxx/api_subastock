<?php

include "./Config/Token.php";
include "./Config/Conexion.php";
$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'POST') {
    $contenido = trim(file_get_contents("php://input"));
    $datos = json_decode($contenido, true);

    // Verificar si el token es válido
    if (isset($datos['token'])) {
        $token = $datos['token'];
        $decodedToken = Token::validateToken($token);

        if ($decodedToken['valid']) {
            $respuesta = formatearRespuesta(true, 'Token válido',  ["data" => $decodedToken['data']]);
        } else {
            $respuesta = formatearRespuesta(false, 'Token inválido');
        }
    } else {
        $respuesta = formatearRespuesta(false, 'Token no proporcionado');
    }

} else {
    $respuesta = formatearRespuesta(false, 'Método no permitido, se esperaba POST');
}

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    header("Content-Type: application/json");
    echo json_encode($respuesta);