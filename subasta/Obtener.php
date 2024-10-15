<?php
include './Config/Conexion.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segmentos_uri = explode('/', $uri);
$idSubasta = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3]) ? $segmentos_uri[3] : null;
$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    try {
        if ($idSubasta) {
            $consultaSubasta = $base_de_datos->prepare("SELECT * FROM subasta WHERE idSubasta = ?");
            $consultaSubasta->execute([$idSubasta]);
            $subasta = $consultaSubasta->fetch(PDO::FETCH_ASSOC);
            
            if ($subasta) {
                $consultaPujaMaxima = $base_de_datos->prepare("SELECT MAX(valor) as pujaMaxima FROM puja WHERE idSubasta = ?");
                $consultaPujaMaxima->execute([$idSubasta]);
                $pujaMaxima = $consultaPujaMaxima->fetch(PDO::FETCH_ASSOC)['pujaMaxima'];

                $valorActual = $pujaMaxima ? $pujaMaxima : $subasta['pujaMinima'];

                $respuesta = formatearRespuesta(true, "Subasta encontrada exitosamente.", [
                    'subasta' => $subasta,
                    'valorActual' => $valorActual, 
                ]);
            } else {
                $respuesta = formatearRespuesta(false, "No se encontró ninguna subasta con el ID especificado.");
            }
        } else {
            $limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? intval($_GET['limit']) : 24;
            $page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
            $search = isset($_GET['search']) ? $_GET['search'] : '';
            $offset = ($page - 1) * $limit;

            $totalQuery = $base_de_datos->prepare("SELECT COUNT(*) as total FROM subasta WHERE tituloSubasta LIKE ?");
            $totalQuery->bindValue(1, "%$search%", PDO::PARAM_STR);
            $totalQuery->execute();
            $totalResultados = $totalQuery->fetch(PDO::FETCH_ASSOC)['total'];
            $totalPaginas = ceil($totalResultados / $limit);

            $consulta = $base_de_datos->prepare("SELECT * FROM subasta WHERE tituloSubasta LIKE ? LIMIT ? OFFSET ?");
            $consulta->bindValue(1, "%$search%", PDO::PARAM_STR);
            $consulta->bindValue(2, $limit, PDO::PARAM_INT);
            $consulta->bindValue(3, $offset, PDO::PARAM_INT);
            $consulta->execute();

            $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
            $respuesta = formatearRespuesta(true, "Subastas obtenidas correctamente.", [
                'subastas' => $resultado,
                'totalPaginas' => $totalPaginas,
                'paginaActual' => $page
            ]);   
        }
    } catch (Exception $e) {
        $respuesta = formatearRespuesta(false, "Error en la consulta SQL: " . $e->getMessage());
    }
} else {
    $respuesta = formatearRespuesta(false, "Método de solicitud no permitido. Se esperaba GET.");
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");
echo json_encode($respuesta);
?>
