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
                'UrlObtener' => 'https://apisubastock.cleverapps.io/usuario/Obtener',
                'UrlInsertar' => 'https://apisubastock.cleverapps.io/usuario/Insertar',
                'UrlActualizar' => 'https://apisubastock.cleverapps.io/usuario/Actualizar',
                'UrlEliminar' => 'https://apisubastock.cleverapps.io/usuario/Eliminar',
                'UrlLogin' => 'https://apisubastock.cleverapps.io/usuario/Login',
            ],
            'puja' =>[
                'UrlObtener' => 'https://apisubastock.cleverapps.io/puja/Obtener',
                'UrlInsertar' => 'https://apisubastock.cleverapps.io/puja/Insertar',
                'UrlActualizar' => 'https://apisubastock.cleverapps.io/puja/Actualizar',
                'UrlEliminar' => 'https://apisubastock.cleverapps.io/puja/Eliminar'
            ],
            'animal' =>[
                'UrlObtener' => 'https://apisubastock.cleverapps.io/animal/Obtener',
                'UrlInsertar' => 'https://apisubastock.cleverapps.io/animal/Insertar',
                'UrlActualizar' => 'https://apisubastock.cleverapps.io/animal/Actualizar',
                'UrlEliminar' => 'https://apisubastock.cleverapps.io/animal/Eliminar'
            ],
            'medicamento' =>[
                'UrlObtener' => 'https://apisubastock.cleverapps.io/medicamento/Obtener',
                'UrlInsertar' => 'https://apisubastock.cleverapps.io/medicamento/Insertar',
                'UrlActualizar' => 'https://apisubastock.cleverapps.io/medicamento/Actualizar',
                'UrlEliminar' => 'https://apisubastock.cleverapps.io/medicamento/Eliminar'
            ],
            'favorito' =>[
                'UrlObtener' => 'https://apisubastock.cleverapps.io/favorito/Obtener',
                'UrlInsertar' => 'https://apisubastock.cleverapps.io/favorito/Insertar',
                'UrlActualizar' => 'https://apisubastock.cleverapps.io/favorito/Actualizar',
                'UrlEliminar' => 'https://apisubastock.cleverapps.io/favorito/Eliminar'
            ],
            'alimentacion' =>[
                'UrlObtener' => 'https://apisubastock.cleverapps.io/alimentacion/Obtener',
                'UrlInsertar' => 'https://apisubastock.cleverapps.io/alimentacion/Insertar',
                'UrlActualizar' => 'https://apisubastock.cleverapps.io/alimentacion/Actualizar',
                'UrlEliminar' => 'https://apisubastock.cleverapps.io/alimentacion/Eliminar'
            ],
            'subasta' =>[
                'UrlObtener' => 'https://apisubastock.cleverapps.io/subasta/Obtener',
                'UrlInsertar' => 'https://apisubastock.cleverapps.io/subasta/Insertar',
                'UrlActualizar' => 'https://apisubastock.cleverapps.io/subasta/Actualizar',
                'UrlEliminar' => 'https://apisubastock.cleverapps.io/subasta/Eliminar',
                'urlUsuarioPujador' => 'https://apisubastock.cleverapps.io/subasta/PujasPorAnimal'
            ],
            'EstadoSalud' => [
                'UrlObtener' => 'https://apisubastock.cleverapps.io/estadoSalud/Obtener',
                'UrlInsertar' => 'https://apisubastock.cleverapps.io/estadoSalud/Insertar',
                'UrlActualizar' => 'https://apisubastock.cleverapps.io/estadoSalud/Actualizar',
                'UrlEliminar' => 'https://apisubastock.cleverapps.io/estadoSalud/Eliminar'
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
