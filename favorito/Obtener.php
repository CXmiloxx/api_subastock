<?php
    include './Config/Conexion.php';

    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segmentos_uri = explode('/', $uri);

    $idUsuario = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3]) ? $segmentos_uri[3] : null;

    $metodo = $_SERVER['REQUEST_METHOD'];


    if($metodo == 'GET') {
        try{
            if($idFavorito) {

                $consulta = $base_de_datos->prepare("SELECT * FROM favorito WHERE idFavorito = ?");
                $consulta->execute([$idFavorito]);
                $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

                if($resultado){
                    $respuesta = formatearRespuesta(true, "Favorito encontrado exitosamente.", ['Favorito' => $resultado]);

                } else {
                    $respuesta = formatearRespuesta(false, "No se encontró ningún Favorito con el ID especificado.");
                }

            } else {
                $consulta = $base_de_datos->query("SELECT * FROM favorito");
                $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
                $respuesta = formatearRespuesta(true, "Favoritos obtenidos correctamente.", ['favoritos' => $resultado]);
            }

        } catch (Exception $e){
            $respuesta = formatearRespuesta(false, "Error en la consulta SQL: " . $e->getMessage());
        }

    } else{
        $respuesta = formatearRespuesta(false, "Método de solicitud no permitido. Se esperaba GET.");
    }

header('Content-Type: application/json');
echo json_encode($respuesta);

?>