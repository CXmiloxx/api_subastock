<?php
include './Config/Conexion.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segmentos_uri = explode('/', $uri);

$idUsuario = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3]) ? $segmentos_uri[3] : null;

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    if ($idUsuario) {
        try {
            $consulta = $base_de_datos->prepare("
                SELECT *
                FROM subasta 
                WHERE subasta.idUsuario = :idUsuario
            ");
            $consulta->execute(['idUsuario' => $idUsuario]);

            $SubastasDeUsuario = $consulta->fetchAll(PDO::FETCH_ASSOC);

            if ($SubastasDeUsuario) {
                echo json_encode([
                    'status' => true,
                    'message' => 'Subastas obtenidas correctamente.',
                    'Subastas' => $SubastasDeUsuario
                ]);
            } else {
                echo json_encode([
                    'status' => false,
                    'message' => 'No se encontraron subastas para este usuario.'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => 'Error en la consulta SQL: ' . $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            'status' => false,
            'message' => 'ID de usuario no proporcionado o inválido.'
        ]);
    }
} else {
    echo json_encode([
        'status' => false,
        'message' => 'Método de solicitud no permitido. Se esperaba GET.'
    ]);
}
?>
