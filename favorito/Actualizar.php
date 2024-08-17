<?php
    include './Config/Conexion.php';

    $metodo = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segmentos_uri = explode('/', $uri);
    $idFavorito = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3]) ? $segmentos_uri[3] : null;

    if($metodo == 'PUT'){
        if($idFavorito){
        $contenido = trim(file_get_contents('php://input'));
        $datos = json_decode($contenido,true);

            if(!empty($datos['idFavorito']) && !empty($datos['idSubasta']) && !empty($datos['idFavorito']) ){
                $idFavorito = $datos['idFavorito'];
                $idSubasta = $datos['idSubasta'];
                $idFavorito = $datos['idFavorito'];

                try{
                    $favoritoExiste = $base_de_datos->prepare("SELECT COUNT(*) FROM favorito WHERE idFavorito = ?");
                    $favoritoExiste->execute([$idFavorito]);

                    if($favoritoExiste->fetchColumn()){
                        $consulta = $base_de_datos->prepare("UPDATE favorito SET idSubasta = :idS, idFavorito = :idU WHERE idFavorito = :idF");
                        $consulta->bindParam(':idS', $idSubasta);
                        $consulta->bindParam(':idU', $idFavorito);
                        $consulta->bindParam(':idF', $idFavorito);
                        $proceso = $consulta->execute();

                        if($proceso && $consulta->rowCount()){
                            $respuesta = formatearRespuesta(true, "Favorito actualizado correctamente.");
                        
                        } else {
                            $respuesta = formatearRespuesta(false, "No se pudo actualizar el favorito. Verifica los datos y vuelve a intentarlo.");
                        }

                    } else{
                        $respuesta = formatearRespuesta(false, "El favorito con el ID especificado no existe.");
                    }

                } catch(Exception $e){
                    $respuesta = formatearRespuesta(false, "Error en la consulta SQL: " . $e->getMessage());
                }

            } else {
                $respuesta = formatearRespuesta(false, "Datos incompletos o inválidos. Asegúrate de enviar todos los campos requeridos.");
            }
        } else {
            $respuesta = formatearRespuesta(false, "Debe especificar un ID de usuario en la ruta.");
        }

    } else {
        $respuesta = formatearRespuesta(false, "Método de solicitud no permitido. Se esperaba PUT.");
    }

header('Content-Type: application/json');
echo json_encode($respuesta);

?>