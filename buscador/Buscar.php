<?php
include '../Config/Conexion.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segmentos_uri = explode('/', $uri);
$idUsuario = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3]) ? $segmentos_uri[3] : null;
$textoBusqueda = isset($segmentos_uri[4]) ? $segmentos_uri[4] : null;
$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    try {
        if ($idUsuario) {
            $consulta = $base_de_datos->prepare("SELECT * FROM animal WHERE idUsuario = ? AND (marca LIKE ? OR raza LIKE ?)");
            $consulta->execute([$idUsuario, "%$textoBusqueda%", "%$textoBusqueda%"]);
            $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);

            if ($resultado) {
                $respuesta = formatearRespuesta(true, "Animales encontrados exitosamente.", ['animal' => $resultado]);
            } else {
                $respuesta = formatearRespuesta(false, "No se encontraron animales que coincidan con el texto de búsqueda.");
            }
        } else {
            $respuesta = formatearRespuesta(false, "ID de usuario no proporcionado.");
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
?>
