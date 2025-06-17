<?php
require_once 'BaseController.php';

class ProductsController extends BaseController {
    private $productModel;
    
    public function __construct() {
        parent::__construct();
        $this->productModel = $this->model('Product');
    }
    
    // Listar todos los productos
    public function index() {
        requireAuth();
        
        // Paginación
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $per_page = 10;
        
        $products = $this->productModel->getAll($page, $per_page);
        $total_products = $this->productModel->countAll();
        $total_pages = ceil($total_products / $per_page);
        
        $this->render('products/index', [
            'products' => $products,
            'current_page' => $page,
            'total_pages' => $total_pages,
            'total_products' => $total_products
        ]);
    }
    
    // Mostrar formulario para crear un nuevo producto
    public function create() {
        requireAuth();
        
        // Obtener categorías para el select
        $categories = $this->getCategories();
        
        $this->render('products/create', [
            'categories' => $categories,
            'product' => null,
            'errors' => []
        ]);
    }
    
    // Guardar un nuevo producto
    public function store() {
        requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateProduct($_POST);
            
            if (empty($errors)) {
                $data = [
                    'category_id' => $_POST['category_id'],
                    'code' => $_POST['code'],
                    'name' => $_POST['name'],
                    'description' => $_POST['description'] ?? '',
                    'price' => $_POST['price'],
                    'cost' => $_POST['cost'] ?? 0,
                    'stock' => $_POST['stock'] ?? 0,
                    'min_stock' => $_POST['min_stock'] ?? 5
                ];
                
                if ($this->productModel->create($data)) {
                    $_SESSION['success_message'] = 'Producto creado exitosamente';
                    redirect('products');
                } else {
                    $errors[] = 'Error al crear el producto. Por favor, inténtalo de nuevo.';
                }
            }
        } else {
            $errors = [];
        }
        
        // Obtener categorías para el select
        $categories = $this->getCategories();
        
        $this->render('products/create', [
            'categories' => $categories,
            'product' => $_POST,
            'errors' => $errors
        ]);
    }
    
    // Mostrar un producto
    public function show($id) {
        requireAuth();
        
        $product = $this->productModel->getById($id);
        
        if (!$product) {
            http_response_code(404);
            require_once __DIR__ . '/../views/errors/404.php';
            return;
        }
        
        $this->render('products/show', [
            'product' => $product
        ]);
    }
    
    // Mostrar formulario para editar un producto
    public function edit($id) {
        requireAuth();
        
        $product = $this->productModel->getById($id);
        
        if (!$product) {
            $_SESSION['error_message'] = 'Producto no encontrado';
            redirect('products');
        }
        
        // Obtener categorías para el select
        $categories = $this->getCategories();
        
        $this->render('products/edit', [
            'product' => $product,
            'categories' => $categories,
            'errors' => []
        ]);
    }
    
    // Actualizar un producto
    public function update($id) {
        requireAuth();
        
        $product = $this->productModel->getById($id);
        
        if (!$product) {
            $_SESSION['error_message'] = 'Producto no encontrado';
            redirect('products');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateProduct($_POST, $id);
            
            if (empty($errors)) {
                $data = [
                    'category_id' => $_POST['category_id'],
                    'code' => $_POST['code'],
                    'name' => $_POST['name'],
                    'description' => $_POST['description'] ?? '',
                    'price' => $_POST['price'],
                    'cost' => $_POST['cost'] ?? 0,
                    'stock' => $_POST['stock'] ?? 0,
                    'min_stock' => $_POST['min_stock'] ?? 5
                ];
                
                if ($this->productModel->update($id, $data)) {
                    $_SESSION['success_message'] = 'Producto actualizado exitosamente';
                    redirect('products');
                } else {
                    $errors[] = 'Error al actualizar el producto. Por favor, inténtalo de nuevo.';
                }
            }
        } else {
            $errors = [];
        }
        
        // Obtener categorías para el select
        $categories = $this->getCategories();
        
        $this->render('products/edit', [
            'product' => array_merge($product, $_POST),
            'categories' => $categories,
            'errors' => $errors
        ]);
    }
    
    // Eliminar un producto
    public function delete($id) {
        requireAuth();
        
        if ($this->productModel->delete($id)) {
            $_SESSION['success_message'] = 'Producto eliminado exitosamente';
        } else {
            $_SESSION['error_message'] = 'Error al eliminar el producto';
        }
        
        redirect('/products');
    }
    
    // Validar los datos del producto
    private function validateProduct($data, $exclude_id = null) {
        $errors = [];
        
        if (empty($data['code'])) {
            $errors['code'] = 'El código es obligatorio';
        } elseif ($this->productModel->codeExists($data['code'], $exclude_id)) {
            $errors['code'] = 'El código ya está en uso';
        }
        
        if (empty($data['name'])) {
            $errors['name'] = 'El nombre es obligatorio';
        }
        
        if (empty($data['category_id'])) {
            $errors['category_id'] = 'La categoría es obligatoria';
        }
        
        if (!isset($data['price']) || $data['price'] === '') {
            $errors['price'] = 'El precio es obligatorio';
        } elseif (!is_numeric($data['price']) || $data['price'] < 0) {
            $errors['price'] = 'El precio debe ser un número mayor o igual a 0';
        }
        
        if (isset($data['stock']) && !is_numeric($data['stock'])) {
            $errors['stock'] = 'El stock debe ser un número';
        }
        
        if (isset($data['min_stock']) && !is_numeric($data['min_stock'])) {
            $errors['min_stock'] = 'El stock mínimo debe ser un número';
        }
        
        return $errors;
    }
    
    // Obtener todas las categorías
    private function getCategories() {
        $query = "SELECT id, name FROM categories ORDER BY name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
