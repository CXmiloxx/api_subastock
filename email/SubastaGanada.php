<?php

require '../vendor/autoload.php';
include '../Config/Conexion.php';
$resend = Resend::client('re_5kRW8qJK_6FrN9bZQvKmSMrFzjGuskNgk');

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segmentos_uri = explode('/', $uri);
$idSubasta = isset($segmentos_uri[4]) && is_numeric($segmentos_uri[4]) ? $segmentos_uri[4] : null;
$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    try {
        if ($idSubasta) {
            $consultaSubasta = $base_de_datos->prepare("SELECT * FROM subasta s JOIN usuario u ON u.idUsuario = s.idUsuario WHERE idSubasta = ? AND fechaFin >= NOW()");
            $consultaSubasta->execute([$idSubasta]);
            $subasta = $consultaSubasta->fetch(PDO::FETCH_ASSOC);

            if ($subasta) {
                $consultaPujaMaxima = $base_de_datos->prepare("SELECT puja.idUsuario FROM puja WHERE idSubasta = ? ORDER BY valor DESC LIMIT 1");
                $consultaPujaMaxima->execute([$idSubasta]);
                $pujaMaxima = $consultaPujaMaxima->fetch(PDO::FETCH_ASSOC);

                if($pujaMaxima){
                    $consultaUsuarioPuja = $base_de_datos->prepare("SELECT * FROM usuario WHERE idUsuario = ?");
                    // TODO -> Cambiar constante 4 por $pujaMaxima["idUsuario"]
                    $consultaUsuarioPuja->execute([4]);
                    $usuarioPuja = $consultaUsuarioPuja->fetch(PDO::FETCH_ASSOC);

                    if($usuarioPuja){
                        $resend->emails->send([
                            'from' => 'Acme <onboarding@resend.dev>',
                            'to' => [$usuarioPuja['correo']],
                            'subject' => 'Subasta ganada',
                            'html' => '<p>Has ganado la subasta '.$subasta['tituloSubasta'].', para mayor información contacta al número '.$subasta['telefono'].'</p>'
                        ]);

                        $respuesta = formatearRespuesta(true, "Correo enviado exitosamente.", [
                            'subasta' => $subasta,
                            'pujaMaxima' => $pujaMaxima,
                            'usuarioPuja' => $usuarioPuja
                        ]);
                    }
                    else {
                        $respuesta = formatearRespuesta(false, "Usuario de puja no encontrado.");
                    }
                }
                else {
                    $respuesta = formatearRespuesta(false, "La subasta no tiene pujas.");
                }
            } else {
                $respuesta = formatearRespuesta(false, "No se encontró ninguna subasta con el ID especificado.");
            }
        }
        else {
            $respuesta = formatearRespuesta(false, "Se necesita un id de subasta para continuar.");
        }
    } catch (Exception $e) {
        $respuesta = formatearRespuesta(false, "Error en la consulta SQL: " . $e->getMessage());
    }
} else {
    $respuesta = formatearRespuesta(false, "Método de solicitud no permitido. Se esperaba GET.");
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");
echo json_encode($respuesta);