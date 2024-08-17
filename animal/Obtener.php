<?php

    include './Config/Conexion.php';

    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segmentos_uri = explode('/', $uri);
    $idAnimal = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3]) ? $segmentos_uri[3] : null;
    $metodo = $_SERVER['REQUEST_METHOD'];

    if ($metodo === 'GET') {
        try {

            if ($idAnimal) {
                $consulta = $base_de_datos->prepare("SELECT * FROM animal WHERE idAnimal = ?");
                $consulta->execute([$idAnimal]);
                $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

                if ($resultado) {
                    $respuesta = formatearRespuesta(true, "Usuario encontrado exitosamente.", ['animal' => $resultado]);
                } else {
                    $respuesta = formatearRespuesta(false, "No se encontró ningún animal con el ID especificado.");
                }
            } else {
                $consulta = $base_de_datos->query("SELECT * FROM animal");
                $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);

                $respuesta = formatearRespuesta(true, "Animales obtenidos correctamente.", ['animales' => $resultado]);
            }
        } catch (Exception $e) {
            $respuesta = formatearRespuesta(false, "Error en la consulta SQL: " . $e->getMessage());
        }
    } else {
        $respuesta = formatearRespuesta(false, "Método de solicitud no permitido. Se esperaba GET.");
    }

header('Content-Type: application/json');
echo json_encode($respuesta);

?>
