<?php
class BaseController {
    protected $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // Método para cargar vistas
    protected function render($view, $data = []) {
        // Extraer los datos para que estén disponibles en la vista
        extract($data);
        
        // Incluir el archivo de la vista
        $viewFile = 'app/views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            // Vista no encontrada
            require_once 'app/views/errors/404.php';
        }
    }
    
    // Método para cargar modelos
    // Verificar si el usuario está autenticado
    protected function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit();
        }
    }
    
    // Mostrar página de error 404
    protected function notFound($message = 'Página no encontrada') {
        http_response_code(404);
        $this->render('errors/404', [
            'title' => 'Error 404',
            'message' => $message
        ]);
        exit();
    }
    
    // Cargar un modelo
    protected function model($model) {
        $modelFile = 'app/models/' . $model . '.php';
        
        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model($this->db);
        }
        
        return null;
    }
}
