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
    protected function model($model) {
        $modelFile = 'app/models/' . $model . '.php';
        
        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model($this->db);
        }
        
        return null;
    }
}
