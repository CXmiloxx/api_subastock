<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");
$request = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$resource = isset($request[0]) ? $request[0] : null;
$action = isset($request[1]) ? $request[1] : $_SERVER['REQUEST_METHOD'];

if (empty($resource)) {
    echo json_encode([
        'status' => true,
        'message' => 'Bienvenido a la API',
        'detalles' => [

            'usuario' => [
                'UrlObtener' => 'http://localhost:8000/usuario/Obtener',
                'UrlInsertar' => 'http://localhost:8000/usuario/Insertar',
                'UrlActualizar' => 'http://localhost:8000/usuario/Actualizar',
                'UrlEliminar' => 'http://localhost:8000/usuario/Eliminar',
                'UrlLogin' => 'http://localhost:8000/usuario/Login',
            ],
            'puja' =>[
                'UrlObtener' => 'http://localhost:8000/puja/Obtener',
                'UrlInsertar' => 'http://localhost:8000/puja/Insertar',
                'UrlActualizar' => 'http://localhost:8000/puja/Actualizar',
                'UrlEliminar' => 'http://localhost:8000/puja/Eliminar'
            ],
            'animal' =>[
                'UrlObtener' => 'http://localhost:8000/animal/Obtener',
                'UrlInsertar' => 'http://localhost:8000/animal/Insertar',
                'UrlActualizar' => 'http://localhost:8000/animal/Actualizar',
                'UrlEliminar' => 'http://localhost:8000/animal/Eliminar'
            ],
            'medicamento' =>[
                'UrlObtener' => 'http://localhost:8000/medicamento/Obtener',
                'UrlInsertar' => 'http://localhost:8000/medicamento/Insertar',
                'UrlActualizar' => 'http://localhost:8000/medicamento/Actualizar',
                'UrlEliminar' => 'http://localhost:8000/medicamento/Eliminar'
            ],
            'favorito' =>[
                'UrlObtener' => 'http://localhost:8000/favorito/Obtener',
                'UrlInsertar' => 'http://localhost:8000/favorito/Insertar',
                'UrlActualizar' => 'http://localhost:8000/favorito/Actualizar',
                'UrlEliminar' => 'http://localhost:8000/favorito/Eliminar'
            ],
            'alimentacion' =>[
                'UrlObtener' => 'http://localhost:8000/alimentacion/Obtener',
                'UrlInsertar' => 'http://localhost:8000/alimentacion/Insertar',
                'UrlActualizar' => 'http://localhost:8000/alimentacion/Actualizar',
                'UrlEliminar' => 'http://localhost:8000/alimentacion/Eliminar'
            ],
            'subasta' =>[
                'UrlObtener' => 'http://localhost:8000/subasta/Obtener',
                'UrlInsertar' => 'http://localhost:8000/subasta/Insertar',
                'UrlActualizar' => 'http://localhost:8000/subasta/Actualizar',
                'UrlEliminar' => 'http://localhost:8000/subasta/Eliminar'
            ],
            'EstadoSalud' => [
                'UrlObtener' => 'http://localhost:8000/estadoSalud/Obtener',
                'UrlInsertar' => 'http://localhost:8000/estadoSalud/Insertar',
                'UrlActualizar' => 'http://localhost:8000/estadoSalud/Actualizar',
                'UrlEliminar' => 'http://localhost:8000/estadoSalud/Eliminar'
            ]
        ]
    ]);
    exit;
}

$filepath = "$resource/$action.php";
if (file_exists($filepath)) {
    require $filepath;
} else {
    echo json_encode([
        'status' => false,
        'message' => 'El archivo correspondiente a la acción y recurso solicitados no existe.',
        'details' => [
            'resource' => $resource,
            'action' => $action,
            'filepath' => $filepath
        ]
    ]);
}
?>
