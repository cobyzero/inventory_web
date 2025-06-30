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
$router->get('/user', 'UserController@index');
$router->get('/', function() {
    if (!isset($_SESSION['role'])) {
        header('Location: /auth/login');
        exit();
    }
    if ($_SESSION['role'] === 'admin') {
        header('Location: /dashboard');
        exit();
    } else {
        header('Location: /user');
        exit();
    }
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
$router->post('/sales/removeFromCart', 'SalesController@removeFromCart');
$router->get('/sales/{id}/edit', 'SalesController@edit'); // Solo admin
$router->post('/sales/{id}/update', 'SalesController@update'); // Solo admin
$router->post('/sales/{id}/cancel', 'SalesController@cancel');
$router->post('/sales/addToCart', 'SalesController@addToCart');
$router->get('/sales/{id}/show', 'SalesController@show');

// Manejar rutas no encontradas
$router->setNotFound(function() {
    http_response_code(404);
    require __DIR__ . '/app/views/errors/404.php';
});

// Ejecutar el enrutador
$router->dispatch();