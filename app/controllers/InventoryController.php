<?php
require_once 'BaseController.php';

class InventoryController extends BaseController {
    private $inventoryModel;
    private $productModel;
    
    public function __construct() {
        parent::__construct();
        $this->inventoryModel = $this->model('Inventory');
        $this->productModel = $this->model('Product');
    }
    
    // Listar todos los movimientos de inventario
    public function index() {
        // Paginación
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $per_page = 15;
        
        $movements = $this->inventoryModel->getAll($page, $per_page);
        $total_movements = $this->inventoryModel->countAll();
        $total_pages = ceil($total_movements / $per_page);
        
        $this->render('inventory/index', [
            'title' => 'Movimientos de Inventario',
            'movements' => $movements,
            'current_page' => $page,
            'total_pages' => $total_pages,
            'total_items' => $total_movements,
            'movement_types' => $this->inventoryModel::getMovementTypes()
        ]);
    }
    
    // Mostrar formulario para crear movimiento
    public function create() {
        // Obtener lista de productos para el select
        $products = $this->productModel->getAll(1, 1000); // Obtener todos los productos
        
        $this->render('inventory/create', [
            'title' => 'Nuevo Movimiento de Inventario',
            'products' => $products,
            'types' => $this->inventoryModel::getMovementTypes(),
            'movement' => [
                'product_id' => $_GET['product_id'] ?? '',
                'quantity' => '',
                'reference' => '',
                'type'=> '',
                'notes' => '',
                'created_at' => date('Y-m-d\TH:i')
            ],
            'errors' => []
        ]);
    }
    
    // Almacenar nuevo movimiento
    public function store() {
        $data = [
            'product_id' => $_POST['product_id'] ?? '',
            'movement_type' => $_POST['movement_type'] ?? 'entry',
            'quantity' => $_POST['quantity'] ?? 0,
            'reference_id' => !empty($_POST['reference_id']) ? $_POST['reference_id'] : null,
            'reference_type' => !empty($_POST['reference_type']) ? $_POST['reference_type'] : null,
            'notes' => $_POST['notes'] ?? ''
        ];
        
        $errors = $this->validate($data);
        
        if (empty($errors)) {
            try {
                $movement_id = $this->inventoryModel->create($data);
                
                if ($movement_id) {
                    $_SESSION['success'] = 'Movimiento registrado exitosamente';
                    
                    // Redirigir según el botón presionado
                    if (isset($_POST['save_and_new'])) {
                        header('Location: /inventory/create?product_id=' . $data['product_id'] . '&type=' . $data['movement_type']);
                    } else {
                        header('Location: /inventory');
                    }
                    exit();
                } else {
                    $errors['general'] = 'Error al registrar el movimiento. Intente nuevamente.';
                }
            } catch (Exception $e) {
                $errors['general'] = $e->getMessage();
            }
        }
        
        // Si hay errores, volver a mostrar el formulario
        $products = $this->productModel->getAll(1, 1000); // Obtener todos los productos
        
        $this->render('inventory/create', [
            'title' => 'Nuevo Movimiento de Inventario',
            'products' => $products,
            'movement_types' => $this->inventoryModel::getMovementTypes(),
            'movement' => $data,
            'errors' => $errors
        ]);
    }
    
    // Mostrar detalles de un movimiento
    public function show($id) {
        $movement = $this->inventoryModel->getById($id);
        
        if (!$movement) {
            $this->notFound('Movimiento no encontrado');
            return;
        }
        
        $this->render('inventory/show', [
            'title' => 'Detalles del Movimiento #' . $movement['id'],
            'movement' => $movement,
            'movement_types' => $this->inventoryModel::getMovementTypes()
        ]);
    }
    
    // Mostrar historial de un producto
    public function productHistory($product_id) {
        // Verificar que el producto exista
        $product = $this->productModel->getById($product_id);
        
        if (!$product) {
            $this->notFound('Producto no encontrado');
            return;
        }
        
        // Paginación
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $per_page = 15;
        
        $movements = $this->inventoryModel->getProductHistory($product_id, $page, $per_page);
        $total_movements = $this->inventoryModel->countProductHistory($product_id);
        $total_pages = ceil($total_movements / $per_page);
        
        $current_stock = $this->inventoryModel->getCurrentStock($product_id);
        
        $this->render('inventory/product_history', [
            'title' => 'Historial de Inventario: ' . $product['name'],
            'product' => $product,
            'movements' => $movements,
            'current_stock' => $current_stock,
            'current_page' => $page,
            'total_pages' => $total_pages,
            'total_items' => $total_movements,
            'movement_types' => $this->inventoryModel::getMovementTypes()
        ]);
    }
    
    // Validar los datos del formulario
    private function validate($data) {
        $errors = [];
        
        // Validar producto
        if (empty($data['product_id'])) {
            $errors['product_id'] = 'El producto es obligatorio';
        } else {
            $product = $this->productModel->getById($data['product_id']);
            if (!$product) {
                $errors['product_id'] = 'Producto no válido';
            }
        }
        
        // Validar tipo de movimiento
        $valid_types = array_keys($this->inventoryModel::getMovementTypes());
        if (!in_array($data['movement_type'], $valid_types)) {
            $errors['movement_type'] = 'Tipo de movimiento no válido';
        }
        
        // Validar cantidad
        if (!is_numeric($data['quantity']) || $data['quantity'] <= 0) {
            $errors['quantity'] = 'La cantidad debe ser un número mayor a cero';
        }
        
        // Validar referencia si es necesario
        if (in_array($data['movement_type'], ['sale', 'purchase']) && empty($data['reference_id'])) {
            $errors['reference_id'] = 'La referencia es obligatoria para este tipo de movimiento';
        }
        
        return $errors;
    }
}
