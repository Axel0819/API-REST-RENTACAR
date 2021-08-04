<?php
use Slim\Routing\RouteCollectorProxy;

$app->group('/filtro/{tabla}', function (RouteCollectorProxy $filtro){
    $filtro->get('/{indice}/{limite}', 'App\Controllers\Filtrar:obtenerTodos');
    $filtro->get('/{codigo}', 'App\Controllers\Filtrar:porCodigo');
    $filtro->get('/filtrar/{indice}/{limite}', 'App\Controllers\Filtrar:ejecutar');
});

$app->group('/vehiculo', function (RouteCollectorProxy $vehiculo) {
    $vehiculo->post('', 'App\Controllers\Vehiculo:crear');
    $vehiculo->put('/{codigo}', 'App\Controllers\Vehiculo:editar');
    $vehiculo->delete('/{codigo}', 'App\Controllers\Vehiculo:borrar');
});

$app->group('/cliente', function (RouteCollectorProxy $cliente) {
    $cliente->post('', 'App\Controllers\Clientes:crear');
    $cliente->put('/{codigo}', 'App\Controllers\Clientes:editar');
    $cliente->delete('/{codigo}', 'App\Controllers\Clientes:borrar');
});

$app->group('/administra', function (RouteCollectorProxy $admin) {
    $admin->post('', 'App\Controllers\Admin:crear');
    $admin->put('/{codigo}', 'App\Controllers\Admin:editar');
    $admin->delete('/{codigo}', 'App\Controllers\Admin:borrar');
});

$app->group('/reservacion', function (RouteCollectorProxy $reservacion) {
    $reservacion->post('', 'App\Controllers\Reservacion:crear');
    $reservacion->put('/{codigo}', 'App\Controllers\Reservacion:editar');
    $reservacion->delete('/{codigo}', 'App\Controllers\Reservacion:borrar');
});

$app->group('/usuario', function (RouteCollectorProxy $usuario) {
    $usuario->put('/pas/{usr}', 'App\Controllers\Usuario:cambiarPassw');
    $usuario->put('/reset/{usr}', 'App\Controllers\Usuario:resetPassw');
});

$app->group('/auth', function (RouteCollectorProxy $auth) {
    $auth->post('/iniciar', 'App\Controllers\Auth:iniciarSesion');
    $auth->post('/cerrar', 'App\Controllers\Auth:cerrarSesion');
    $auth->post('/refrescar', 'App\Controllers\Auth:refrescarTokens');
});
