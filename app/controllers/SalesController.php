<?php
require_once 'BaseController.php';

class SalesController extends BaseController {
    private $saleModel;
    private $productModel;
    private $customerModel;
    
    public function __construct() {
        parent::__construct();
        $this->saleModel = $this->model('Sale');
        $this->productModel = $this->model('Product');
        $this->customerModel = $this->model('Customer');
    }
    
    // Listar todas las ventas
    public function index() {
        // Paginación
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $per_page = 15;
        
        $sales = $this->saleModel->getAll($page, $per_page);
        $total_sales = $this->saleModel->countAll();
        $total_pages = ceil($total_sales / $per_page);
        
        $this->render('sales/index', [
            'title' => 'Ventas',
            'sales' => $sales,
            'current_page' => $page,
            'total_pages' => $total_pages,
            'total_items' => $total_sales,
            'payment_methods' => $this->saleModel::getPaymentMethods(),
            'statuses' => $this->saleModel::getStatuses()
        ]);
    }
    
    // Mostrar formulario para crear venta
    public function create() {
        // Obtener lista de productos y clientes
        $products = $this->productModel->getAll(1, 1000); // Obtener todos los productos
        $customers = $this->customerModel->getAll(1, 1000); // Obtener todos los clientes
        
        $this->render('sales/create', [
            'title' => 'Nueva Venta',
            'products' => $products,
            'customers' => $customers,
            'sale' => [
                'customer_id' => $_GET['customer_id'] ?? '',
                'payment_method' => 'cash',
                'status' => 'completed',
                'notes' => '',
                'items' => []
            ],
            'payment_methods' => $this->saleModel::getPaymentMethods(),
            'statuses' => $this->saleModel::getStatuses(),
            'errors' => []
        ]);
    }
    
    // Almacenar nueva venta
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /sales/create');
            exit();
        }
        
        // Procesar datos del formulario
        $data = [
            'customer_id' => $_POST['customer_id'] ?? 1,
            'payment_method' => $_POST['payment_method'] ?? 'cash',
            'status' => $_POST['status'] ?? 'completed',
            'subtotal' => (float)($_POST['subtotal'] ?? 0),
            'tax' => (float)($_POST['tax'] ?? 0),
            'discount' => (float)($_POST['discount'] ?? 0),
            'total' => (float)($_POST['total'] ?? 0),
            'notes' => $_POST['notes'] ?? '',
            'items' => []
        ];
        
        // Procesar ítems
        if (!empty($_POST['items']) && is_array($_POST['items'])) {
            foreach ($_POST['items'] as $item) {
                if (!empty($item['product_id']) && !empty($item['quantity']) && !empty($item['price'])) {
                    $data['items'][] = [
                        'product_id' => (int)$item['product_id'],
                        'quantity' => (int)$item['quantity'],
                        'price' => (float)$item['price']
                    ];
                }
            }
        }
        
        // Validar datos
        $errors = $this->validate($data);
        
        if (empty($errors)) {
            try {
                $sale_id = $this->saleModel->create($data);
                
                if ($sale_id) {
                    $_SESSION['success'] = 'Venta registrada exitosamente';
                    header('Location: /sales/' . $sale_id);
                    exit();
                } else {
                    $errors['general'] = 'Error al registrar la venta. Intente nuevamente.';
                }
            } catch (Exception $e) {
                $errors['general'] = $e->getMessage();
            }
        }
        
        // Si hay errores, volver a mostrar el formulario con los datos ingresados
        $products = $this->productModel->getAll(1, 1000);
        $customers = $this->customerModel->getAll(1, 1000);
        
        $this->render('sales/create', [
            'title' => 'Nueva Venta',
            'products' => $products,
            'customers' => $customers,
            'sale' => $data,
            'payment_methods' => $this->saleModel::getPaymentMethods(),
            'statuses' => $this->saleModel::getStatuses(),
            'errors' => $errors
        ]);
    }
    
    // Mostrar detalles de una venta
    public function show($id) {
        $sale = $this->saleModel->getById($id);
        
        if (!$sale) {
            $this->notFound('Venta no encontrada');
            return;
        }
        
        $this->render('sales/show', [
            'title' => 'Detalles de Venta #' . $sale['invoice_number_formatted'],
            'sale' => $sale,
            'payment_methods' => $this->saleModel::getPaymentMethods(),
            'statuses' => $this->saleModel::getStatuses()
        ]);
    }
    
    // Validar datos del formulario
    private function validate($data) {
        $errors = [];
        
        // Validar cliente
        if (empty($data['customer_id'])) {
            $errors['customer_id'] = 'El cliente es obligatorio';
        } else {
            // Aquí podrías verificar si el cliente existe
        }
        
        // Validar método de pago
        $valid_payment_methods = array_keys($this->saleModel::getPaymentMethods());
        if (!in_array($data['payment_method'], $valid_payment_methods)) {
            $errors['payment_method'] = 'Método de pago no válido';
        }
        
        // Validar estado
        $valid_statuses = array_keys($this->saleModel::getStatuses());
        if (!in_array($data['status'], $valid_statuses)) {
            $errors['status'] = 'Estado no válido';
        }
        
        // Validar montos
        if (!is_numeric($data['subtotal']) || $data['subtotal'] < 0) {
            $errors['subtotal'] = 'Subtotal no válido';
        }
        
        if (!is_numeric($data['tax']) || $data['tax'] < 0) {
            $errors['tax'] = 'Impuesto no válido';
        }
        
        if (!is_numeric($data['discount']) || $data['discount'] < 0) {
            $errors['discount'] = 'Descuento no válido';
        }
        
        if (!is_numeric($data['total']) || $data['total'] < 0) {
            $errors['total'] = 'Total no válido';
        }
        
        // Validar ítems
        if (empty($data['items'])) {
            $errors['items'] = 'Debe agregar al menos un producto a la venta';
        } else {
            foreach ($data['items'] as $index => $item) {
                if (empty($item['product_id']) || empty($item['quantity']) || $item['quantity'] <= 0) {
                    $errors['items'] = 'Cantidad inválida para uno o más productos';
                    break;
                }
                
                if ($item['price'] <= 0) {
                    $errors['items'] = 'Precio inválido para uno o más productos';
                    break;
                }
            }
        }
        
        return $errors;
    }
}
