<?php
include './Config/Conexion.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segmentos_uri = explode('/', $uri);
$idSubasta = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3]) ? $segmentos_uri[3] : null;

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'DELETE') {
    $contenido = trim(file_get_contents("php://input"));
    $datos = json_decode($contenido, true);

    if (!$idSubasta || empty($datos['idUsuario'])) {
        $respuesta = formatearRespuesta(false, "Debe especificar un ID válido de subasta y usuario.");
        echo json_encode($respuesta);
        exit;
    }

    $idUsuario = $datos['idUsuario'];

    try {
        $consulta = $base_de_datos->prepare("DELETE FROM favorito WHERE idSubasta = ? AND idUsuario = ?");
        $proceso = $consulta->execute([$idSubasta, $idUsuario]);

        if ($proceso && $consulta->rowCount()) {
            $respuesta = formatearRespuesta(true, "Favorito eliminado correctamente.");
        } else {
            $respuesta = formatearRespuesta(false, "No se pudo eliminar el favorito. Verifica los IDs.");
        }
    } catch (Exception $e) {
        $respuesta = formatearRespuesta(false, "Error en la consulta SQL: " . $e->getMessage());
    }
} else {
    $respuesta = formatearRespuesta(false, "Método no permitido. Se esperaba DELETE.");
}

echo json_encode($respuesta);
