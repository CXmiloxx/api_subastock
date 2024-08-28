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
        $imagen = $_FILES['imagen'];

        try {
            try {
                $resultado = $cloudinary->uploadApi()->upload($imagen['tmp_name'], [
                    'folder' => 'subastas'
                ]);
            
                if (isset($resultado['secure_url'])) {
                    $imagenUrl = $resultado['secure_url'];
                } else {
                    throw new Exception('No se pudo obtener la URL segura de la imagen.');
                }
            
            } catch (Exception $e) {
                error_log("Cloudinary error: " . $e->getMessage());
                $respuesta = formatearRespuesta(false, "Error en la subida de la imagen a Cloudinary: " . $e->getMessage());
                echo json_encode($respuesta);
                exit;
            }

            $consulta = $base_de_datos->prepare("INSERT INTO subasta (idUsuario, idAnimal, pujaMinima, fechaInicio, fechaFin, imagenUrl, tituloSubasta, descripcion) VALUES (:idU, :idA, :pMin, :fIni, :fFin, :img, :tiS, :des)");
            $consulta->bindParam(':idU', $idUsuario);
            $consulta->bindParam(':idA', $idAnimal);
            $consulta->bindParam(':pMin', $pujaMinima);
            $consulta->bindParam(':fIni', $fechaInicio);
            $consulta->bindParam(':fFin', $fechaFin);
            $consulta->bindParam(':img', $imagenUrl);
            $consulta->bindParam(':tiS', $tituloSubasta);
            $consulta->bindParam(':des', $descripcion);
            $proceso = $consulta->execute();

            if ($proceso) {
                $respuesta = formatearRespuesta(true, "Subasta insertada correctamente.");
            } else {
                $respuesta = formatearRespuesta(false, "No se pudo insertar la subasta. Verifica los datos y vuelve a intentarlo.");
            }
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'upload') !== false) {
                $respuesta = formatearRespuesta(false, "Error en la subida de la imagen a Cloudinary: " . $e->getMessage());
            } else {
                $respuesta = formatearRespuesta(false, "Error en la consulta SQL: " . $e->getMessage());
            }
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
