<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargar configuración
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

// Mostrar errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cargar el enrutador
require_once __DIR__ . '/core/Router.php';

// Crear una instancia del enrutador
$router = new Router();

// Rutas de autenticación
$router->get('/auth/login', 'AuthController@login');
$router->post('/auth/login', 'AuthController@login');
$router->post('/auth/logout', 'AuthController@logout');

// Rutas del dashboard
$router->get('/', function() {
    header('Location: /dashboard');
    exit();
});
$router->get('/dashboard', 'DashboardController@index');

// Rutas de productos
$router->get('/products', 'ProductsController@index');
$router->get('/products/create', 'ProductsController@create');
$router->post('/products', 'ProductsController@store');
$router->put('/products/{id}', 'ProductsController@update');
$router->get('/products/{id}', 'ProductsController@show');
$router->get('/products/{id}/edit', 'ProductsController@edit');
$router->post('/products/{id}/update', 'ProductsController@update');
$router->delete('/products/{id}', 'ProductsController@delete');

// Manejar rutas no encontradas
$router->setNotFound(function() {
    http_response_code(404);
    require __DIR__ . '/app/views/errors/404.php';
});

// Ejecutar el enrutador
$router->dispatch();