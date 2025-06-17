<?php

class Router {
    private $routes = [];
    private $notFoundCallback;
    
    /**
     * Agrega una ruta GET
     */
    public function get($path, $handler) {
        $this->addRoute('GET', $path, $handler);
    }
    
    /**
     * Agrega una ruta POST
     */
    public function post($path, $handler) {
        $this->addRoute('POST', $path, $handler);
    }
    
    /**
     * Agrega una ruta PUT
     */
    public function put($path, $handler) {
        $this->addRoute('PUT', $path, $handler);
    }
    
    /**
     * Agrega una ruta DELETE
     */
    public function delete($path, $handler) {
        $this->addRoute('DELETE', $path, $handler);
    }
    
    /**
     * Agrega una ruta personalizada
     */
    private function addRoute($method, $path, $handler) {
        // Convertir la ruta a una expresión regular
        $pattern = $this->pathToRegex($path);
        $this->routes[$method][$pattern] = $handler;
    }
    
    /**
     * Convierte una ruta con parámetros a una expresión regular
     */
    private function pathToRegex($path) {
        // Si la ruta es la raíz, devolver el patrón para la raíz
        if ($path === '/') {
            return '#^/$#';
        }
        
        // Reemplaza {param} con ([^/]+) y escapa las barras
        $pattern = preg_replace('/\{([^\/]+)\}/', '([^/]+)', $path);
        return '#^' . $pattern . '$#';
    }
    
    /**
     * Maneja una ruta 404 personalizada
     */
    public function setNotFound($callback) {
        $this->notFoundCallback = $callback;
    }
    
    /**
     * Ejecuta el enrutador
     */
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Normalizar la ruta
        $path = rtrim($path, '/');
        if ($path === '') {
            $path = '/';
        }
        
        // Depuración
        error_log("Método: $method, Ruta: $path");
        error_log("Rutas definidas: " . print_r(array_keys($this->routes[$method] ?? []), true));
        
        // Manejar método PUT/DELETE desde formularios
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }
        
        // Verificar si hay rutas definidas para este método
        if (!isset($this->routes[$method])) {
            return $this->handleNotFound();
        }
        
        // Buscar una ruta que coincida
        foreach ($this->routes[$method] as $pattern => $handler) {
            if (preg_match($pattern, $path, $matches)) {
                // Eliminar la coincidencia completa (índice 0)
                array_shift($matches);
                
                // Manejar el controlador y la acción
                $this->handleRoute($handler, $matches);
                return;
            }
        }
        
        // Ninguna ruta coincidió
        $this->handleNotFound();
    }
    
    /**
     * Maneja la ejecución de la ruta
     */
    private function handleRoute($handler, $params = []) {
        // Si es un callable, ejecutarlo directamente
        if (is_callable($handler)) {
            call_user_func_array($handler, $params);
            return;
        }
        
        // Si es un string en formato 'Controller@method'
        if (is_string($handler)) {
            list($controllerName, $method) = explode('@', $handler);
            $controllerFile = __DIR__ . '/../app/controllers/' . $controllerName . '.php';
            
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                $controller = new $controllerName();
                
                if (method_exists($controller, $method)) {
                    call_user_func_array([$controller, $method], $params);
                    return;
                }
            }
        }
        
        // Si no se pudo manejar la ruta
        $this->handleNotFound();
    }
    
    /**
     * Maneja el error 404
     */
    private function handleNotFound() {
        if ($this->notFoundCallback) {
            call_user_func($this->notFoundCallback);
        } else {
            header('HTTP/1.0 404 Not Found');
            echo '404 Not Found';
        }
    }
}

// Función auxiliar para redirigir
function redirect($path) {
    // Si la ruta ya comienza con http, usarla como está
    if (strpos($path, 'http') === 0) {
        $url = $path;
    } 
    // Si la ruta comienza con /, es una ruta absoluta
    elseif (strpos($path, '/') === 0) {
        $url = $path;
    }
    // Si no, es una ruta relativa, usar base_url
    else {
        $url = '/' . ltrim($path, '/');
    }
    
    // Agregar la URL base si no está presente
    if (strpos($url, BASE_URL) !== 0) {
        $url = rtrim(BASE_URL, '/') . '/' . ltrim($url, '/');
    }
    
    // Redirigir
    header('Location: ' . $url);
    exit();
}

// Función para incluir una vista
function view($view, $data = []) {
    extract($data);
    require __DIR__ . '/../app/views/' . $view . '.php';
}

// Función para obtener la URL base
function base_url($path = '') {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script = dirname($_SERVER['SCRIPT_NAME']);
    
    return rtrim("$protocol://$host$script/$path", '/');
}
