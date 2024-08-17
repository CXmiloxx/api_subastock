<?php
include './Config/Conexion.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segmentos_uri = explode('/', $uri);
$idSubasta = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3]) ? $segmentos_uri[3] : null;
$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'DELETE') {
    if ($idSubasta) {
        try {
            $consulta = $base_de_datos->prepare("DELETE FROM subasta WHERE idSubasta = ?");
            $proceso = $consulta->execute([$idSubasta]);

            if ($proceso && $consulta->rowCount()) {
                $respuesta = formatearRespuesta(true, "Subasta eliminada correctamente.");
            } else {
                $respuesta = formatearRespuesta(false, "No se pudo eliminar la subasta. Verifica el ID o si la subasta existe.");
            }
        } catch (Exception $e) {
            $respuesta = formatearRespuesta(false, "Error en la consulta SQL: " . $e->getMessage());
        }
    } else {
        $respuesta = formatearRespuesta(false, "Debe especificar un ID de subasta para eliminar.");
    }
} else {
    $respuesta = formatearRespuesta(false, "Método de solicitud no permitido. Se esperaba DELETE.");
}

header('Content-Type: application/json');
echo json_encode($respuesta);
?>
