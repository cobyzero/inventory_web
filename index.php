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
$router->get('/auth/register', 'AuthController@register');
$router->post('/auth/register', 'AuthController@register');


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

// Rutas de categorías
$router->get('/categories', 'CategoriesController@index');
$router->get('/categories/create', 'CategoriesController@create');
$router->post('/categories', 'CategoriesController@store');
$router->get('/categories/{id}', 'CategoriesController@show');
$router->get('/categories/{id}/edit', 'CategoriesController@edit');
$router->put('/categories/{id}', 'CategoriesController@update');
$router->delete('/categories/{id}', 'CategoriesController@delete');

// Rutas de inventario
$router->get('/inventory', 'InventoryController@index');
$router->get('/inventory/create', 'InventoryController@create');
$router->post('/inventory', 'InventoryController@store');
$router->get('/inventory/{id}', 'InventoryController@show');
$router->get('/products/{id}/inventory', 'InventoryController@productHistory');

// Rutas de ventas
$router->get('/sales', 'SalesController@index');
$router->get('/sales/create', 'SalesController@create');
$router->post('/sales', 'SalesController@store');
$router->post('/sales/store', 'SalesController@store');
$router->get('/sales/{id}', 'SalesController@show');
$router->get('/sales/{id}/edit', 'SalesController@edit');
$router->post('/sales/{id}/update', 'SalesController@update');
$router->post('/sales/{id}/cancel', 'SalesController@cancel');
$router->get('/sales/{id}/print', 'SalesController@print');

// Rutas de clientes
$router->get('/customers', 'CustomersController@index');
$router->get('/customers/create', 'CustomersController@create');
$router->post('/customers', 'CustomersController@store');
$router->get('/customers/{id}', 'CustomersController@show');
$router->get('/customers/{id}/edit', 'CustomersController@edit');
$router->put('/customers/{id}', 'CustomersController@update');
$router->post('/customers/{id}/update', 'CustomersController@update');
$router->delete('/customers/{id}', 'CustomersController@delete');
$router->get('/customers/search', 'CustomersController@search');

// Manejar rutas no encontradas
$router->setNotFound(function() {
    http_response_code(404);
    require __DIR__ . '/app/views/errors/404.php';
});

// Ejecutar el enrutador
$router->dispatch();