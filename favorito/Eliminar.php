<?php
    include './Config/Conexion.php';
    $uri = parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
    $segmentos_uri = explode('/', $uri);

    $idUsuario = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3]) ? $segmentos_uri[3] : null;
    $metodo = $_SERVER['REQUEST_METHOD'];

    if($metodo == 'DELETE'){
        if($idFavorito){

            try{
                $consulta = $base_de_datos->prepare("DELETE FROM favorito WHERE idFavorito = ?");
                $proceso = $consulta->execute([$idFavorito]);
            
                if($proceso && $consulta->rowCount()){
                    $respuesta = formatearRespuesta(true, "Favorito eliminado correctamente.");

                } else {
                    $respuesta = formatearRespuesta(false, "No se pudo eliminar el Favorito. Verifica el ID o si el usuario existe.");
                }

            } catch (Exception $e){
                $respuesta = formatearRespuesta(false, "Error en la consulta SQL: " . $e->getMessage());
            }
        } else {
            $respuesta = formatearRespuesta(false, "Debe especificar un ID de favorito para eliminar.");
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