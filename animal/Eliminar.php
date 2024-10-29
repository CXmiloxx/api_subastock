<?php
include './Config/Conexion.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segmentos_uri = explode('/', $uri);
$idAnimal = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3]) ? $segmentos_uri[3] : null;
$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo == 'DELETE') {
    if ($idAnimal) {
        try {
            $base_de_datos->beginTransaction();

            $consultaPujas = $base_de_datos->prepare("DELETE FROM puja WHERE idSubasta IN (SELECT idSubasta FROM subasta WHERE idAnimal = ?)");
            $consultaPujas->execute([$idAnimal]);

            $consultaFavoritos = $base_de_datos->prepare("DELETE FROM favorito WHERE idSubasta IN (SELECT idSubasta FROM subasta WHERE idAnimal = ?)");
            $consultaFavoritos->execute([$idAnimal]);

            $consultaSubastas = $base_de_datos->prepare("DELETE FROM subasta WHERE idAnimal = ?");
            $consultaSubastas->execute([$idAnimal]);

            $consultaSalud = $base_de_datos->prepare("DELETE FROM estado_salud WHERE idAnimal = ?");
            $consultaSalud->execute([$idAnimal]);

            $consultaAlimentacion = $base_de_datos->prepare("DELETE FROM alimentacion WHERE idAnimal = ?");
            $consultaAlimentacion->execute([$idAnimal]);

            $consultaMedicamento = $base_de_datos->prepare("DELETE FROM medicamento WHERE idAnimal = ?");
            $consultaMedicamento->execute([$idAnimal]);

            $consultaAnimal = $base_de_datos->prepare("DELETE FROM animal WHERE idAnimal = ?");
            $proceso = $consultaAnimal->execute([$idAnimal]);

            if ($proceso && $consultaAnimal->rowCount() != 0) {
                $base_de_datos->commit();
                $respuesta = formatearRespuesta(true, "Animal eliminado correctamente.");
            } else {
                $base_de_datos->rollBack();
                $respuesta = formatearRespuesta(false, "No se pudo eliminar el animal. Verifica el ID o si el animal existe.");
            }
        } catch (Exception $e) {
            $base_de_datos->rollBack();
            $respuesta = formatearRespuesta(false, "Error en la consulta SQL: " . $e->getMessage());
        }
    } else {
        $respuesta = formatearRespuesta(false, "Debe especificar un ID de animal para eliminar.");
    }
} else {
    $respuesta = formatearRespuesta(false, "Método de solicitud no permitido. Se esperaba DELETE.");
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");
echo json_encode($respuesta);


?>
