<?php
include './Config/Conexion.php';

$metodo = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segmentos_uri = explode('/', $uri);

$idAnimal = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3]) ? $segmentos_uri[3] : null;

if ($metodo === 'GET') {
    if ($idAnimal) {
        try {
            $consulta = $base_de_datos->prepare("
                SELECT u.nombres, u.apellidos, p.valor, s.tituloSubasta
                FROM puja p
                JOIN usuario u ON p.idUsuario = u.idUsuario
                JOIN subasta s ON p.idSubasta = s.idSubasta
                WHERE s.idAnimal = :idAnimal
                ORDER BY p.valor DESC
            ");
            $consulta->bindParam(':idAnimal', $idAnimal);
            $consulta->execute();

            $pujas = $consulta->fetchAll(PDO::FETCH_ASSOC);

            if ($pujas) {
                $respuesta = formatearRespuesta(true, "Pujas obtenidas correctamente.", ['pujas' => $pujas]);
            } else {
                $respuesta = formatearRespuesta(false, "No se encontraron pujas para este animal en la subasta.");
            }

        } catch (Exception $e) {
            $respuesta = formatearRespuesta(false, "Error en la consulta SQL: " . $e->getMessage());
        }

    } else {
        $respuesta = formatearRespuesta(false, "ID de animal no proporcionado o inválido.");
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
