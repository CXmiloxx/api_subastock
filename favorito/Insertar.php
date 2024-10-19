<?php
include './Config/Conexion.php';

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo == 'POST') {

    $contenido = trim(file_get_contents("php://input"));
    $datos = json_decode($contenido, true);

    if (isset($datos['idSubasta'], $datos['idUsuario'])) {
        $idSubasta = $datos['idSubasta'];
        $idUsuario = $datos['idUsuario'];

        try {
            $query = 'SELECT COUNT(*) FROM favorito WHERE idSubasta = :idSu AND idUsuario = :idU';
            $favoritoExistente = $base_de_datos->prepare($query);
            $favoritoExistente->bindParam(':idSu', $idSubasta);
            $favoritoExistente->bindParam(':idU', $idUsuario);
            $favoritoExistente->execute();

            $existe = $favoritoExistente->fetchColumn();

            if ($existe > 0) {
                $respuesta = formatearRespuesta(false, "Este producto ya está en tus favoritos.");
            } else {
                $consulta = $base_de_datos->prepare(
                    "INSERT INTO favorito (idSubasta, idUsuario) VALUES (:idS, :idU)"
                );
                $consulta->bindParam(':idS', $idSubasta);
                $consulta->bindParam(':idU', $idUsuario);
                $proceso = $consulta->execute();

                if ($proceso) {
                    $respuesta = formatearRespuesta(true, "Favorito insertado correctamente.");
                } else {
                    $respuesta = formatearRespuesta(false, "No se pudo insertar el favorito. Verifica los datos.");
                }
            }
        } catch (Exception $e) {
            $respuesta = formatearRespuesta(false, "Error en la consulta SQL: " . $e->getMessage());
        }
    } else {
        $respuesta = formatearRespuesta(false, "Datos incompletos o inválidos.");
    }
} else {
    $respuesta = formatearRespuesta(false, "Método no permitido. Se esperaba POST.");
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

echo json_encode($respuesta);
?>
