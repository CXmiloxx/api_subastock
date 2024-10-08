<?php
include './Config/Conexion.php';
include './Config/ConfigCloudary.php';

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo == 'POST') {
    if (isset($_FILES['imagen'], $_POST['idUsuario'], $_POST['idAnimal'], $_POST['pujaMinima'], $_POST['fechaInicio'], $_POST['fechaFin'], $_POST['tituloSubasta'], $_POST['descripcion'])) {
        $idUsuario = $_POST['idUsuario'];
        $idAnimal = $_POST['idAnimal'];
        $pujaMinima = $_POST['pujaMinima'];
        $fechaInicio = $_POST['fechaInicio'];
        $fechaFin = $_POST['fechaFin'];
        $tituloSubasta = $_POST['tituloSubasta'];
        $descripcion = $_POST['descripcion'];

        $imagenesSubidas = [];

        try {
            $query = 'SELECT COUNT(*) FROM subasta WHERE idAnimal= :idAni';
            $consultaId = $base_de_datos->prepare($query);
            $consultaId->bindParam(':idAni', $idAnimal);
            $consultaId->execute();

            if ($consultaId->fetchColumn() > 0) {
                $respuesta = formatearRespuesta(false, 'El animal ya está siendo subastado.');
            } else {
                $resultado = $cloudinary->uploadApi()->upload($_FILES['imagen']['tmp_name'], ['folder' => 'subastas']);
                if (isset($resultado['secure_url'])) {
                    $imagenesSubidas[] = $resultado['secure_url'];
                } else {
                    throw new Exception('No se pudo obtener la URL segura de la imagen principal.');
                }

                for ($i = 1; $i <= 5; $i++) {
                    $imagenKey = "imagen" . $i;

                    if (isset($_FILES[$imagenKey]) && $_FILES[$imagenKey]['tmp_name']) {
                        $resultado = $cloudinary->uploadApi()->upload($_FILES[$imagenKey]['tmp_name'], ['folder' => 'subastas']);
                        if (isset($resultado['secure_url'])) {
                            $imagenesSubidas[] = $resultado['secure_url'];
                        }
                    } else {
                        $imagenesSubidas[] = null;
                    }
                }

                $consulta = $base_de_datos->prepare("
                    INSERT INTO subasta (idUsuario, idAnimal, pujaMinima, fechaInicio, fechaFin, imagenUrl, imagenUrl2, imagenUrl3, imagenUrl4, imagenUrl5, tituloSubasta, descripcion)
                    VALUES (:idU, :idA, :pMin, :fIni, :fFin, :img1, :img2, :img3, :img4, :img5, :tiS, :des)
                ");

                $consulta->bindParam(':idU', $idUsuario);
                $consulta->bindParam(':idA', $idAnimal);
                $consulta->bindParam(':pMin', $pujaMinima);
                $consulta->bindParam(':fIni', $fechaInicio);
                $consulta->bindParam(':fFin', $fechaFin);
                $consulta->bindParam(':img1', $imagenesSubidas[0]);
                $consulta->bindParam(':img2', $imagenesSubidas[1]);
                $consulta->bindParam(':img3', $imagenesSubidas[2]);
                $consulta->bindParam(':img4', $imagenesSubidas[3]);
                $consulta->bindParam(':img5', $imagenesSubidas[4]);
                $consulta->bindParam(':tiS', $tituloSubasta);
                $consulta->bindParam(':des', $descripcion);

                $proceso = $consulta->execute();

                if ($proceso) {
                    $respuesta = formatearRespuesta(true, "Subasta insertada correctamente.");
                } else {
                    $respuesta = formatearRespuesta(false, "No se pudo insertar la subasta. Verifica los datos y vuelve a intentarlo.");
                }
            }
        } catch (Exception $e) {
            $respuesta = formatearRespuesta(false, "Error: " . $e->getMessage());
        }
    } else {
        $respuesta = formatearRespuesta(false, "Datos incompletos o inválidos. Asegúrate de enviar todos los campos requeridos.");
    }
} else {
    $respuesta = formatearRespuesta(false, "Método de solicitud no permitido. Se esperaba POST.");
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");
echo json_encode($respuesta);
