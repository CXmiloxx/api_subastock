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
            $resultado = $cloudinary->uploadApi()->upload($_FILES['imagen']['tmp_name'], ['folder' => 'subastas']);
            if (isset($resultado['secure_url'])) {
                $imagenesSubidas[] = $resultado['secure_url'];
            } else {
                throw new Exception('No se pudo obtener la URL segura de la imagen principal.');
            }
        } catch (Exception $e) {
            error_log("Cloudinary error: " . $e->getMessage());
            $respuesta = formatearRespuesta(false, "Error en la subida de la imagen a Cloudinary: " . $e->getMessage());
            echo json_encode($respuesta);
            exit;
        }

        for ($i = 1; $i <= 5; $i++) {
            $imagenkey = "imagen" . $i;
            
            if (isset($_FILES[$imagenkey]) && $_FILES[$imagenkey]['tmp_name']) {
                try {
                    $resultado = $cloudinary->uploadApi()->upload($_FILES[$imagenkey]['tmp_name'], ['folder' => 'subastas']);
                    if (isset($resultado['secure_url'])) {
                        $imagenesSubidas[] = $resultado['secure_url'];
                    }
                } catch (Exception $e) {
                    error_log("Cloudinary error: " . $e->getMessage());
                    $respuesta = formatearRespuesta(false, "Error en la subida de la imagen opcional: " . $e->getMessage());
                    echo json_encode($respuesta);
                    exit;
                }
            } else {
                $imagenesSubidas[] = null;
            }
        }

        try {
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
        } catch (Exception $e) {
            $respuesta = formatearRespuesta(false, "Error en la consulta SQL: " . $e->getMessage());
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
?>
