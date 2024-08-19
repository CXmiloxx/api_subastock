<?php
include './Config/Conexion.php';

$metodo = $_SERVER['REQUEST_METHOD'];

    if($metodo == 'POST'){

        $contendido = trim(file_get_contents("php://input"));
        $datos = json_decode($contendido, true);

        if( isset ( $datos['idSubasta'], $datos['idUsuario'] ) ){
            $idSubasta = $datos['idSubasta'];
            $idUsuario = $datos['idUsuario'];

            try{
                $consulta = $base_de_datos->prepare("INSERT INTO favorito (idSubasta, idUsuario) VALUES (:idS, : idU)");
                $consulta->bindParam(':idS', $idSubasta);
                $consulta->bindParam(':idU', $idUsuario);
                $proceso = $consulta->execute();

                if($proceso){
                    $respuesta = formatearRespuesta(true, "Favorito insertado correctamente.");

                } else {
                    $respuesta = formatearRespuesta(false, "No se pudo insertar el Favorito. Verifica los datos y vuelve a intentarlo.");
                }

            } catch(Exception $e) {
                $respuesta = formatearRespuesta(false, "Error en la consulta SQL: " . $e->getMessage());
            }

        } else {
            $respuesta = formatearRespuesta(false, "Datos incompletos o inválidos. Asegúrate de enviar todos los campos requeridos.");
        }

    } else { 
        $respuesta = formatearRespuesta(false, "Método de solicitud no permitido. Se esperaba POST.");
    }

header('Content-Type: application/json');
echo json_encode($respuesta);

?>