<?php
include './Config/Conexion.php';

$metodo = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segmentos_uri = explode('/', $uri);
$idSubasta = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3]) ? $segmentos_uri[3] : null;

if ($metodo === 'PUT') {
    if ($idSubasta) {
        $contenido = trim(file_get_contents("php://input"));
        $datos = json_decode($contenido, true);

        if (isset($datos['pujaMinima'], $datos['fechaInicio'], $datos['fechaFin'])) {
            $pujaMinima = $datos['pujaMinima'];
            $fechaInicio = $datos['fechaInicio'];
            $fechaFin = $datos['fechaFin'];

            try {
                $subastaExiste = $base_de_datos->prepare("SELECT COUNT(*) FROM subasta WHERE idSubasta = ?");
                $subastaExiste->execute([$idSubasta]);
                if ($subastaExiste->fetchColumn()) {
                    $consulta = $base_de_datos->prepare("UPDATE subasta SET pujaMinima = :pMin, fechaInicio = :fIni, fechaFin = :fFin WHERE idSubasta = :idS");
                    $consulta->bindParam(':pMin', $pujaMinima);
                    $consulta->bindParam(':fIni', $fechaInicio);
                    $consulta->bindParam(':fFin', $fechaFin);
                    $consulta->bindParam(':idS', $idSubasta);
                    $proceso = $consulta->execute();

                    if ($proceso) {
                        $respuesta = formatearRespuesta(true, "Subasta actualizada correctamente.");
                    } else {
                        $respuesta = formatearRespuesta(false, "No se pudo actualizar la subasta. Verifica los datos y vuelve a intentarlo.");
                    }
                } else {
                    $respuesta = formatearRespuesta(false, "Subasta con el ID especificado no existe.");
                }
            } catch (Exception $e) {
                $respuesta = formatearRespuesta(false, "Error en la consulta SQL: " . $e->getMessage());
            }
        } else {
            $respuesta = formatearRespuesta(false, "Datos incompletos o inválidos. Asegúrate de enviar todos los campos requeridos.");
        }
    } else {
        $respuesta = formatearRespuesta(false, "Debe especificar un ID de subasta en la ruta.");
    }
} else {
    $respuesta = formatearRespuesta(false, "Método de solicitud no permitido. Se esperaba PUT.");
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");
echo json_encode($respuesta);
?>
