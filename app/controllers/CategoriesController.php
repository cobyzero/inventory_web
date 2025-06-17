<?php
require_once 'BaseController.php';

class CategoriesController extends BaseController {
    private $categoryModel;
    
    public function __construct() {
        parent::__construct();
        $this->categoryModel = $this->model('Category');
        
        // Verificar autenticación para todas las rutas excepto las públicas
        $this->requireAuth();
    }
    // Listar todas las categorías
    public function index() {
        // Paginación
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $per_page = 10;
        
        $categories = $this->categoryModel->getAll($page, $per_page);
        $total_categories = $this->categoryModel->countAll();
        $total_pages = ceil($total_categories / $per_page);
        
        $this->render('categories/index', [
            'title' => 'Categorías',
            'categories' => $categories,
            'current_page' => $page,
            'total_pages' => $total_pages,
            'total_items' => $total_categories
        ]);
    }

    // Mostrar formulario de creación
    public function create() {
        $this->render('categories/create', [
            'title' => 'Nueva Categoría',
            'category' => ['name' => '', 'description' => ''],
            'errors' => []
        ]);
    }

    // Almacenar nueva categoría
    public function store() {
        $data = [
            'name' => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? ''
        ];
        
        $errors = [];
        
        // Validación
        if (empty($data['name'])) {
            $errors['name'] = 'El nombre es obligatorio';
        } elseif ($this->categoryModel->nameExists($data['name'])) {
            $errors['name'] = 'Ya existe una categoría con este nombre';
        }
        
        if (empty($errors)) {
            try {
                $categoryId = $this->categoryModel->create($data);
                
                if ($categoryId) {
                    $_SESSION['success'] = 'Categoría creada exitosamente';
                    header('Location: /categories');
                    exit();
                } else {
                    $errors['general'] = 'Error al crear la categoría. Intente nuevamente.';
                }
            } catch (Exception $e) {
                $errors['general'] = $e->getMessage();
            }
        }
        
        // Si hay errores, volver a mostrar el formulario
        $this->render('categories/create', [
            'title' => 'Nueva Categoría',
            'category' => $data,
            'errors' => $errors
        ]);
    }

    // Mostrar detalles de una categoría
    public function show($id) {
        $category = $this->categoryModel->getById($id);
        
        if (!$category) {
            $this->notFound('Categoría no encontrada');
            return;
        }
        
        $this->render('categories/show', [
            'title' => $category['name'],
            'category' => $category
        ]);
    }

    // Mostrar formulario de edición
    public function edit($id) {
        $category = $this->categoryModel->getById($id);
        
        if (!$category) {
            $this->notFound('Categoría no encontrada');
            return;
        }
        
        $this->render('categories/edit', [
            'title' => 'Editar Categoría: ' . $category['name'],
            'category' => $category,
            'errors' => []
        ]);
    }

    // Actualizar categoría
    public function update($id) {
        $category = $this->categoryModel->getById($id);
        
        if (!$category) {
            $this->notFound('Categoría no encontrada');
            return;
        }
        
        $data = [
            'name' => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? ''
        ];
        
        $errors = [];
        
        // Validación
        if (empty($data['name'])) {
            $errors['name'] = 'El nombre es obligatorio';
        } elseif ($this->categoryModel->nameExists($data['name'], $id)) {
            $errors['name'] = 'Ya existe otra categoría con este nombre';
        }
        
        if (empty($errors)) {
            try {
                $result = $this->categoryModel->update($id, $data);
                
                if ($result) {
                    $_SESSION['success'] = 'Categoría actualizada exitosamente';
                    header('Location: /categories/');
                    exit();
                } else {
                    $errors['general'] = 'Error al actualizar la categoría. Intente nuevamente.';
                }
            } catch (Exception $e) {
                $errors['general'] = $e->getMessage();
            }
        }
        
        // Si hay errores, volver a mostrar el formulario
        $this->render('categories/', [
            'title' => 'Editar Categoría: ' . ($data['name'] ?? ''),
            'category' => array_merge($category, $data),
            'errors' => $errors
        ]);
    }

    // Manejar solicitudes PUT (actualización)
    public function put($id) {
        $this->update($id);
    }
    
    // Manejar solicitudes DELETE
    public function delete($id) {
        // Verificar si es una solicitud POST con _method=DELETE
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['_method']) || $_POST['_method'] !== 'DELETE') {
            $this->notFound('Método no permitido');
            return;
        }
        
        try {
            $result = $this->categoryModel->delete($id);
            
            if ($result) {
                $_SESSION['success'] = 'Categoría eliminada exitosamente';
            } else {
                $_SESSION['error'] = 'No se pudo eliminar la categoría';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        
        header('Location: /categories');
        exit();
    }
}
