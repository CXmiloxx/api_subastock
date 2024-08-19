<?php
include './Config/Conexion.php';

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'POST') {
    $contenido = trim(file_get_contents("php://input"));
    $datos = json_decode($contenido, true);

    if ( isset ( $datos['idSubasta'], $datos['idUsuario'], $datos['valor'] ) ) {
        $idSubasta = $datos['idSubasta'];
        $idUsuario = $datos['idUsuario'];
        $valor = $datos['valor'];

        try {
            $consulta = $base_de_datos->prepare("INSERT INTO puja (idSubasta, idUsuario, valor) VALUES (:idSub, :idUsu, :val)");
            $consulta->bindParam(':idSub', $idSubasta);
            $consulta->bindParam(':idUsu', $idUsuario);
            $consulta->bindParam(':val', $valor);
            $proceso = $consulta->execute();

            if ($proceso) {
                $respuesta = formatearRespuesta(true, "La puja se ha insertado correctamente.");
            } else {
                $respuesta = formatearRespuesta(false, "No se pudo insertar la puja. Verifica los datos y vuelve a intentarlo.");
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

header('Content-Type: application/json');
echo json_encode($respuesta);
?>
