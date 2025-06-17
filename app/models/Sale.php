<?php
class Sale {
    private $conn;
    private $table_name = "sales";
    private $items_table = "sale_items";
    private $customers_table = "customers";
    private $products_table = "products";
    private $users_table = "users";
    private $inventory_model;

    public function __construct($db) {
        $this->conn = $db;
        // Incluir y crear instancia del modelo de inventario
        require_once __DIR__ . '/Inventory.php';
        $this->inventory_model = new Inventory($db);
    }

    // Obtener todas las ventas con paginación
    public function getAll($page = 1, $per_page = 10) {
        $offset = ($page - 1) * $per_page;
        
        $query = "SELECT s.*, c.name as customer_name, u.username as user_name,
                         CONCAT('V-', LPAD(s.id, 6, '0')) as invoice_number_formatted
                 FROM " . $this->table_name . " s
                 LEFT JOIN " . $this->customers_table . " c ON s.customer_id = c.id
                 LEFT JOIN " . $this->users_table . " u ON s.user_id = u.id
                 ORDER BY s.created_at DESC, s.id DESC
                 LIMIT :offset, :per_page";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':per_page', $per_page, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Contar el total de ventas
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['total'];
    }

    // Obtener una venta por ID
    public function getById($id) {
        // Obtener encabezado de la venta
        $query = "SELECT s.*, c.name as customer_name, c.email as customer_email, 
                         c.phone as customer_phone, c.address as customer_address,
                         u.username as user_name,
                         CONCAT('V-', LPAD(s.id, 6, '0')) as invoice_number_formatted
                 FROM " . $this->table_name . " s
                 LEFT JOIN " . $this->customers_table . " c ON s.customer_id = c.id
                 LEFT JOIN " . $this->users_table . " u ON s.user_id = u.id
                 WHERE s.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $sale = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$sale) {
            return null;
        }
        
        // Obtener ítems de la venta
        $query = "SELECT si.*, p.name as product_name, p.code as product_code, 
                         p.price as product_price, p.stock as current_stock
                 FROM " . $this->items_table . " si
                 JOIN " . $this->products_table . " p ON si.product_id = p.id
                 WHERE si.sale_id = :sale_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sale_id', $id);
        $stmt->execute();
        $sale['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $sale;
    }

    // Crear una nueva venta
    public function create($data) {
        $this->conn->beginTransaction();
        
        try {
            // 1. Crear el encabezado de la venta
            $query = "INSERT INTO " . $this->table_name . " 
                     (customer_id, user_id, invoice_number, subtotal, tax, discount, total, 
                      payment_method, status, notes, created_at)
                     VALUES (:customer_id, :user_id, :invoice_number, :subtotal, :tax, :discount, :total, 
                             :payment_method, :status, :notes, NOW())";
            
            $stmt = $this->conn->prepare($query);
            
            // Validar y limpiar datos
            $customer_id = !empty($data['customer_id']) ? (int)$data['customer_id'] : 1; // Cliente genérico
            $user_id = $_SESSION['user_id'] ?? 1; // Usuario actual o 1 por defecto
            $invoice_number = $this->generateInvoiceNumber();
            $subtotal = (float)$data['subtotal'];
            $tax = (float)$data['tax'];
            $discount = (float)($data['discount'] ?? 0);
            $total = (float)$data['total'];
            $payment_method = $data['payment_method'] ?? 'cash';
            $status = $data['status'] ?? 'completed';
            $notes = htmlspecialchars(strip_tags($data['notes'] ?? ''));
            
            $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':invoice_number', $invoice_number);
            $stmt->bindParam(':subtotal', $subtotal);
            $stmt->bindParam(':tax', $tax);
            $stmt->bindParam(':discount', $discount);
            $stmt->bindParam(':total', $total);
            $stmt->bindParam(':payment_method', $payment_method);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':notes', $notes);
            
            if (!$stmt->execute()) {
                throw new Exception('Error al crear la venta');
            }
            
            $sale_id = $this->conn->lastInsertId();
            
            // 2. Procesar ítems de la venta
            if (empty($data['items']) || !is_array($data['items'])) {
                throw new Exception('No se han proporcionado ítems para la venta');
            }
            
            $query = "INSERT INTO " . $this->items_table . " 
                     (sale_id, product_id, quantity, price, subtotal)
                     VALUES (:sale_id, :product_id, :quantity, :price, :subtotal)";
            $stmt = $this->conn->prepare($query);
            
            foreach ($data['items'] as $item) {
                if (empty($item['product_id']) || empty($item['quantity'])) {
                    continue;
                }
                
                $product_id = (int)$item['product_id'];
                $quantity = (int)$item['quantity'];
                $price = (float)$item['price'];
                $item_subtotal = $quantity * $price;
                
                // Verificar stock disponible
                $product = $this->getProduct($product_id);
                if (!$product) {
                    throw new Exception("Producto con ID $product_id no encontrado");
                }
                
                if ($product['stock'] < $quantity) {
                    throw new Exception("Stock insuficiente para el producto: " . $product['name'] . 
                                      ". Stock actual: " . $product['stock']);
                }
                
                // Insertar ítem de venta
                $stmt->bindParam(':sale_id', $sale_id, PDO::PARAM_INT);
                $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
                $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
                $stmt->bindParam(':price', $price);
                $stmt->bindParam(':subtotal', $item_subtotal);
                
                if (!$stmt->execute()) {
                    throw new Exception('Error al guardar los ítems de la venta');
                }
                
                // 3. Actualizar inventario
                $inventory_data = [
                    'product_id' => $product_id,
                    'movement_type' => 'sale',
                    'quantity' => $quantity * -1, // Negativo porque es una salida
                    'reference_id' => $sale_id,
                    'reference_type' => 'sale',
                    'notes' => 'Venta #' . $sale_id
                ];
                
                $this->inventory_model->create($inventory_data);
            }
            
            $this->conn->commit();
            return $sale_id;
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }
    
    // Obtener un producto por ID
    private function getProduct($id) {
        $query = "SELECT * FROM " . $this->products_table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Generar número de factura
    private function generateInvoiceNumber() {
        $prefix = 'FAC-';
        $year = date('Y');
        $month = date('m');
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " 
                 WHERE YEAR(created_at) = :year AND MONTH(created_at) = :month";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year', $year, PDO::PARAM_INT);
        $stmt->bindParam(':month', $month, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $sequence = str_pad(($row['total'] ?? 0) + 1, 5, '0', STR_PAD_LEFT);
        return $prefix . $year . $month . '-' . $sequence;
    }
    
    // Obtener métodos de pago
    public static function getPaymentMethods() {
        return [
            'cash' => 'Efectivo',
            'credit_card' => 'Tarjeta de Crédito',
            'debit_card' => 'Tarjeta de Débito',
            'transfer' => 'Transferencia Bancaria'
        ];
    }
    
    // Obtener estados de venta
    public static function getStatuses() {
        return [
            'pending' => 'Pendiente',
            'completed' => 'Completada',
            'cancelled' => 'Cancelada'
        ];
    }
}
