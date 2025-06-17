<?php
// Configuración de la aplicación
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/');
define('DEFAULT_CONTROLLER', 'auth');
define('DEFAULT_ACTION', 'login');


// Función para cargar automáticamente las clases
spl_autoload_register(function($class) {
    $paths = [
        'app/controllers/' . $class . '.php',
        'app/models/' . $class . '.php',
        'config/' . $class . '.php'
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Función para verificar si el usuario está autenticado
function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

// Función para requerir autenticación
function requireAuth() {
    if (!isAuthenticated()) {
        redirect('auth/login');
    }
}
