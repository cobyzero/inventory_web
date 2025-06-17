<?php
require_once 'BaseController.php';
class CustomersController extends BaseController {
    private $customerModel;
    
    public function __construct() {
        parent::__construct();
        $this->customerModel = new Customer($this->db);
    }
    
    // Listar todos los clientes
    public function index() {
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $per_page = 15;
        
        $customers = $this->customerModel->getAll($page, $per_page);
        $total_customers = $this->customerModel->countAll();
        $total_pages = ceil($total_customers / $per_page);
        
        $this->render('customers/index', [
            'title' => 'Clientes',
            'customers' => $customers,
            'current_page' => $page,
            'total_pages' => $total_pages,
            'total_customers' => $total_customers
        ]);
    }
    
    // Mostrar formulario para crear un nuevo cliente
    public function create() {
        
        $this->render('customers/create', [
            'title' => 'Nuevo Cliente'
        ]);
    }
    
    // Almacenar un nuevo cliente
    public function store() {
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar datos del formulario
            $errors = [];
            
            if (empty($_POST['name'])) {
                $errors[] = 'El nombre del cliente es obligatorio';
            }
            
            if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'El formato del correo electrónico no es válido';
            }
            
            if (empty($errors)) {
                $customerData = [
                    'name' => $_POST['name'],
                    'email' => $_POST['email'] ?? null,
                    'phone' => $_POST['phone'] ?? null,
                    'address' => $_POST['address'] ?? null
                ];
                
                $customerId = $this->customerModel->create($customerData);
                
                if ($customerId) {
                    $_SESSION['success'] = 'Cliente creado correctamente';
                    
                    // Si es una solicitud AJAX (por ejemplo, desde el modal de ventas)
                    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                        header('Content-Type: application/json');
                        echo json_encode([
                            'success' => true,
                            'customer' => [
                                'id' => $customerId,
                                'name' => $customerData['name'],
                                'email' => $customerData['email']
                            ]
                        ]);
                        exit;
                    } else {
                        // Redirigir a la lista de clientes
                        header('Location: /customers');
                        exit;
                    }
                } else {
                    $errors[] = 'Error al crear el cliente';
                }
            }
            
            // Si hay errores y es AJAX
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('HTTP/1.1 400 Bad Request');
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'errors' => $errors
                ]);
                exit;
            }
            
            // Si hay errores y no es AJAX, volver al formulario con los errores
            $this->render('customers/create', [
                'title' => 'Nuevo Cliente',
                'errors' => $errors,
                'old' => $_POST
            ]);
        } else {
            // Si no es POST, redirigir al formulario
            header('Location: /customers/create');
        }
    }
    
    // Mostrar detalles de un cliente
    public function show($id) {
        
        
        $customer = $this->customerModel->getById($id);
        
        if (!$customer) {
            $this->notFound('Cliente no encontrado');
            return;
        }
        
        $this->render('customers/show', [
            'title' => 'Detalles del Cliente',
            'customer' => $customer
        ]);
    }
    
    // Mostrar formulario para editar un cliente
    public function edit($id) {

        $customer = $this->customerModel->getById($id);
        
        if (!$customer) {
            $this->notFound('Cliente no encontrado');
            return;
        }
        
        $this->render('customers/edit', [
            'title' => 'Editar Cliente',
            'customer' => $customer
        ]);
    }
    
    // Actualizar un cliente existente
    public function update($id) {
        
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar datos del formulario
            $errors = [];
            
            if (empty($_POST['name'])) {
                $errors[] = 'El nombre del cliente es obligatorio';
            }
            
            if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'El formato del correo electrónico no es válido';
            }
            
            if (empty($errors)) {
                $customerData = [
                    'name' => $_POST['name'],
                    'email' => $_POST['email'] ?? null,
                    'phone' => $_POST['phone'] ?? null,
                    'address' => $_POST['address'] ?? null
                ];
                
                $result = $this->customerModel->update($id, $customerData);
                
                if ($result) {
                    $_SESSION['success'] = 'Cliente actualizado correctamente';
                    header('Location: /customers/' . $id);
                    exit;
                } else {
                    $errors[] = 'Error al actualizar el cliente';
                }
            }
            
            // Si hay errores, volver al formulario con los errores
            $this->render('customers/edit', [
                'title' => 'Editar Cliente',
                'errors' => $errors,
                'customer' => array_merge(['id' => $id], $_POST)
            ]);
        } else {
            // Si no es POST, redirigir al listado
            header('Location: /customers');
        }
    }
    
    // Eliminar un cliente
    public function delete($id) {
        
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->customerModel->delete($id);
            
            if ($result) {
                $_SESSION['success'] = 'Cliente eliminado correctamente';
            } else {
                $_SESSION['error'] = 'No se pudo eliminar el cliente. Asegúrese de que no tenga ventas asociadas.';
            }
            
            header('Location: /customers');
            exit;
        } else {
            // Si no es POST, redirigir al listado
            header('Location: /customers');
        }
    }
    
    // Búsqueda de clientes (para autocompletado en ventas)
    public function search() {
        $term = $_GET['term'] ?? '';
        $customers = $this->customerModel->search($term);
        
        header('Content-Type: application/json');
        echo json_encode($customers);
        exit;
    }
}
