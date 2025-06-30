<?php
require_once 'BaseController.php';

class SalesController extends BaseController
{
    // ...
    // Métodos existentes

    // Editar venta (solo admin)
    public function edit($id)
    {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /auth/login');
            exit();
        }
        $sale = $this->saleModel->getById($id);
        if (!$sale) {
            $this->notFound('Venta no encontrada');
            return;
        }
        $products = $this->productModel->getAll(1, 1000);
        $customers = $this->userModel->getAllUsersByRole('user');
        $this->render('sales/edit', [
            'title' => 'Editar Venta',
            'sale' => $sale,
            'products' => $products,
            'customers' => $customers,
            'statuses' => $this->saleModel::getStatuses(),
            'errors' => []
        ]);
    }

    // Actualizar venta (solo admin)
    public function update($id)
    {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /auth/login');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /sales/' . $id . '/edit');
            exit();
        }
        $data = [
            'customer_id' => $_POST['customer_id'] ?? '',
            'status' => $_POST['status'] ?? 'pending',
            'subtotal' => isset($_POST['subtotal']) ? (float)$_POST['subtotal'] : 0,
            'total' => isset($_POST['total']) ? (float)$_POST['total'] : 0,
            'items' => $_POST['items'] ?? []
        ];
        $errors = $this->validate($data);
        if (empty($errors)) {
            try {
                $result = $this->saleModel->update($id, $data);
                if ($result) {
                    header('Location: /sales/' . $id . '/show');
                    exit();
                } else {
                    $errors['general'] = 'Error al actualizar la venta. Intente nuevamente.';
                }
            } catch (Exception $e) {
                $errors['general'] = $e->getMessage();
            }
        }
        $products = $this->productModel->getAll(1, 1000);
        $customers = $this->userModel->getAllUsersByRole('user');
        $sale = $this->saleModel->getById($id);
        $this->render('sales/edit', [
            'title' => 'Editar Venta',
            'sale' => array_merge($sale, $data),
            'products' => $products,
            'customers' => $customers,
            'statuses' => $this->saleModel::getStatuses(),
            'errors' => $errors
        ]);
    }

    private $saleModel;
    private $productModel;
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->saleModel = $this->model('Sale');
        $this->productModel = $this->model('Product');
        $this->userModel = $this->model('User');
    }

    // Listar todas las ventas
    public function index()
    {
        // Paginación
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
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

            'statuses' => $this->saleModel::getStatuses()
        ]);
    }

    // Mostrar formulario para crear venta
    public function create()
    {
        if (!isset($_SESSION['role'])) {
            header('Location: /auth/login');
            exit();
        }
        $products = $this->productModel->getAll(1, 1000); // Obtener todos los productos

        if ($_SESSION['role'] === 'admin') {
            // Obtener todos los usuarios con rol 'user' para seleccionar como "clientes"
            $customers = $this->userModel->getAllUsersByRole('user');
            $sale = [
                'customer_id' => $_GET['customer_id'] ?? '',

                'status' => 'completed',

                'items' => []
            ];
        } else {
            // Solo puede asignarse a sí mismo
            $user_id = $_SESSION['user_id'];
            $user = $this->userModel->getById($user_id);
            $customers = [$user];
            $sale = [
                'customer_id' => $user ? $user['id'] : '',

                'status' => 'completed',

                'items' => []
            ];
        }
        $this->render('sales/create', [
            'title' => 'Nueva Venta',
            'products' => $products,
            'customers' => $customers,
            'sale' => $sale,

            'statuses' => $this->saleModel::getStatuses(),
            'errors' => []
        ]);
    }

    public function calculateTotal()
    {
        $total = 0;
        if (!empty($_SESSION['sale_cart'])) {
            foreach ($_SESSION['sale_cart'] as $item) {
                $total += $item['price'] * $item['qty'];
            }
        }
        return $total;
    }

    public function calculateItems()
    {
        $items = [];
        if (!empty($_SESSION['sale_cart'])) {
            foreach ($_SESSION['sale_cart'] as $item) {
                $items[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['qty'],
                    'price' => $item['price']
                ];
            }
        }
        return $items;
    }
    // Almacenar nueva venta
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /sales/create');
            exit();
        }
        // Procesar datos del formulario
        if (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin') {
            // Forzar customer_id al del usuario logueado
            $user_id = $_SESSION['user_id'];
            $customer_id = $user_id;
        } else {
            $customer_id = $_POST['customer_id'] ?? 1;
        } // No se usa ninguna sesión de ventas, solo flujo directo DB
        $data = [
            'customer_id' => $customer_id,
            'status' => 'pending',
            'subtotal' => (float) $this->calculateTotal(),
            'total' => (float) $this->calculateTotal(),
            'items' => $this->calculateItems()
        ];

        // Validar datos
        $errors = $this->validate($data);

        if (empty($errors)) {
            try {
                // Limpiar carrito de sesión después de guardar
                if (isset($_SESSION['sale_cart'])) {
                    unset($_SESSION['sale_cart']);
                }

                $sale_id = $this->saleModel->create($data);

                if ($sale_id) {
                    $_SESSION['success'] = 'Venta registrada exitosamente';
                    header('Location: /sales/show/' . $sale_id);
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
        $customers = $this->userModel->getAllUsersByRole('user');
        $this->render('sales/create', [
            'title' => 'Nueva Venta',
            'products' => $products,
            'customers' => $customers,
            'sale' => $data,

            'statuses' => $this->saleModel::getStatuses(),
            'errors' => $errors
        ]); // No hay sesión de ventas, solo flujo directo DB
    }

    // Mostrar detalles de una venta
    public function show($id)
    {
        $sale = $this->saleModel->getById($id);

        if (!$sale) {
            $this->notFound('Venta no encontrada');
            return;
        }

        $this->render('sales/show', [
            'title' => 'Detalles de Venta #' . $sale['invoice_number_formatted'],
            'sale' => $sale,

            'statuses' => $this->saleModel::getStatuses()
        ]);
    }

    // Validar datos del formulario
    private function validate($data)
    {
        // Validación igual que antes

        $errors = [];

        // Validar cliente
        if (empty($data['customer_id'])) {
            $errors['customer_id'] = 'El cliente es obligatorio';
        } else {
            // Aquí podrías verificar si el cliente existe
        }

        // Validar montos
        if (!is_numeric($data['subtotal']) || $data['subtotal'] < 0) {
            $errors['subtotal'] = 'Subtotal no válido';
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

    public function addToCart()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /sales/create');
            exit();
        }

        $product_id = $_POST['add_product_id'];
        $product_name = $_POST['add_product_name'];
        $product_code = $_POST['add_product_code'];
        $product_price = $_POST['add_product_price'];
        $product_qty = $_POST['add_product_qty'];

        if (!isset($_SESSION['sale_cart'])) {
            $_SESSION['sale_cart'] = [];
        }

        if (isset($_SESSION['sale_cart'][$product_id])) {
            $_SESSION['sale_cart'][$product_id]['qty'] += (int) $product_qty;
        } else {
            $_SESSION['sale_cart'][$product_id] = [
                'product_id' => $product_id,
                'name' => $product_name,
                'code' => $product_code,
                'price' => $product_price,
                'qty' => $product_qty
            ];
        }

        header('Location: /sales/create');
        exit();
    }

    public function removeFromCart()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /sales/create');
            exit();
        }
        $product_id = $_POST['remove_product_id'] ?? null;
        if ($product_id && isset($_SESSION['sale_cart'][$product_id])) {
            unset($_SESSION['sale_cart'][$product_id]);
        }
        header('Location: /sales/create');
        exit();
    }
}
