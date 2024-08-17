<?php

    include './Config/Conexion.php';

    $metodo = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segmentos_uri = explode('/', $uri);
    $idUsuario = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3]) ? $segmentos_uri[3] : null;


    if ($metodo == 'PUT') {
        $contenido = trim(file_get_contents("php://input"));
        $datos = json_decode($contenido, true);

        if (!empty($datos['idAnimal']) && !empty($datos['idUsuario']) && !empty($datos['marca']) && !empty($datos['raza']) && !empty($datos['especie'])) {
            $idAnimal = $datos['idAnimal'];
            $idUsuario = $datos['idUsuario'];
            $marca = $datos['marca'];
            $raza = $datos['raza'];
            $especie = $datos['especie'];

            try {
                $animalExiste = $base_de_datos->prepare("SELECT COUNT(*) FROM animal WHERE idAnimal = ?");
                $animalExiste->execute([$idAnimal]);
                $existe = $animalExiste->fetchColumn();

                if($existe){
                    $consulta = $base_de_datos->prepare("UPDATE Animal SET idUsuario = :idU, marca = :mar, raza = :raza, especie = :esp WHERE idAnimal = :idA");
                    $consulta->bindParam(':idA', $idAnimal);
                    $consulta->bindParam(':idU', $idUsuario);
                    $consulta->bindParam(':mar', $marca);
                    $consulta->bindParam(':raza', $raza);
                    $consulta->bindParam(':esp', $especie);
                    $proceso = $consulta->execute();

                    if ($proceso && $consulta->rowCount()) {
                        $respuesta = formatearRespuesta(true, "Animal actualizado correctamente.");

                    } else {
                        $respuesta = formatearRespuesta(false, "No se pudo actualizar el animal. Verifica los datos y vuelve a intentarlo.");
                    }
                }else{
                    $respuesta = formatearRespuesta(false, "Animal con el ID especificado no existe.");
                }
            
            } catch (Exception $e) {
                $respuesta = formatearRespuesta(false, "Error en la consulta SQL: ". $e->getMessage());
            }
        } else {
            $respuesta = formatearRespuesta(false, "Datos incompletos o inválidos. Asegúrate de enviar todos los campos requeridos.");
        }
    } else {
        $respuesta = formatearRespuesta(false, "Método de solicitud no permitido. Se esperaba PUT.");
    }

    header('Content-Type: application/json');
    echo json_encode($respuesta);
?>