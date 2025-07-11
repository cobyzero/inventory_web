<?php
class Inventory {
    private $conn;
    private $table_name = "inventory_movements";

    // Tipos de movimiento
    const TYPE_ENTRY = 'entry';
    const TYPE_EXIT = 'exit';
    const TYPE_ADJUSTMENT = 'adjustment';
    const TYPE_SALE = 'sale';
    const TYPE_PURCHASE = 'purchase';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todos los movimientos de inventario con paginación
    public function getAll($page = 1, $per_page = 10) {
        $offset = ($page - 1) * $per_page;
        
        $query = "SELECT im.*, p.name as product_name, p.code as product_code, 
                         u.username as user_name
                 FROM " . $this->table_name . " im
                 LEFT JOIN products p ON im.product_id = p.id
                 LEFT JOIN users u ON im.user_id = u.id
                 ORDER BY im.created_at DESC, im.id DESC
                 LIMIT :offset, :per_page";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':per_page', $per_page, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Contar el total de movimientos
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['total'];
    }

    // Obtener un movimiento por ID
    public function getById($id) {
        $query = "SELECT im.*, p.name as product_name, p.code as product_code, 
                         u.username as user_name
                 FROM " . $this->table_name . " im
                 LEFT JOIN products p ON im.product_id = p.id
                 LEFT JOIN users u ON im.user_id = u.id
                 WHERE im.id = :id 
                 LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear un nuevo movimiento de inventario
    public function create($data) {
        try {
            // 1. Insertar el movimiento
            $query = "INSERT INTO " . $this->table_name . " 
                     (product_id, type, quantity, reference_id, reference_type, notes, user_id, created_at)
                     VALUES (:product_id, :type, :quantity, :reference_id, :reference_type, :notes, :user_id, NOW())";
            
            $stmt = $this->conn->prepare($query);
            
            // Sanitizar datos
            $product_id = (int)$data['product_id'];
            $type = $this->validateMovementType($data['movement_type']);
            $quantity = (int)$data['quantity'];
            $reference_id = !empty($data['reference_id']) ? (int)$data['reference_id'] : null;
            $reference_type = $data['reference_type'] ?? null;
            $notes = htmlspecialchars(strip_tags($data['notes'] ?? ''));
            $user_id = $_SESSION['user_id'] ?? 1; // Usar 1 como usuario por defecto si no hay sesión
            
            // Ajustar el signo de la cantidad según el tipo de movimiento
            if (in_array($type, [self::TYPE_EXIT, self::TYPE_SALE])) {
                $quantity = abs($quantity) * -1; // Las salidas y ventas son negativas
            } else {
                $quantity = abs($quantity); // Las entradas, ajustes y compras son positivos
            }
            
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            $stmt->bindParam(':reference_id', $reference_id, PDO::PARAM_INT);
            $stmt->bindParam(':reference_type', $reference_type);
            $stmt->bindParam(':notes', $notes);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            
            if (!$stmt->execute()) {
                throw new Exception('Error al crear el movimiento de inventario');
            }
            
            $movement_id = $this->conn->lastInsertId();
            
            // 2. Actualizar el stock del producto
            $this->updateProductStock($product_id, $quantity);
            
            return $movement_id;
            
        } catch (Exception $e) {
            throw $e;
        }
    }

    // Actualizar el stock de un producto
    private function updateProductStock($product_id, $quantity) {
        $query = "UPDATE products 
                 SET stock = stock + :quantity,
                     updated_at = NOW()
                 WHERE id = :product_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':product_id', $product_id);
        
        if (!$stmt->execute()) {
            throw new Exception('Error al actualizar el stock del producto');
        }
        
        return true;
    }

    // Obtener el historial de movimientos de un producto
    public function getProductHistory($product_id, $page = 1, $per_page = 10) {
        $offset = ($page - 1) * $per_page;
        
        $query = "SELECT im.*, u.username as user_name,
                         CASE 
                             WHEN im.reference_type = 'sale' THEN CONCAT('Venta #', im.reference_id)
                             WHEN im.reference_type = 'purchase' THEN CONCAT('Compra #', im.reference_id)
                             ELSE im.notes
                         END as reference_text
                 FROM " . $this->table_name . " im
                 LEFT JOIN users u ON im.user_id = u.id
                 WHERE im.product_id = :product_id
                 ORDER BY im.created_at DESC, im.id DESC
                 LIMIT :offset, :per_page";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':per_page', $per_page, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Contar el total de movimientos de un producto
    public function countProductHistory($product_id) {
        $query = "SELECT COUNT(*) as total 
                 FROM " . $this->table_name . " 
                 WHERE product_id = :product_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['total'];
    }

    // Validar el tipo de movimiento
    private function validateMovementType($type) {
        $valid_types = [
            self::TYPE_ENTRY, 
            self::TYPE_EXIT, 
            self::TYPE_ADJUSTMENT,
            self::TYPE_SALE,
            self::TYPE_PURCHASE
        ];
        return in_array($type, $valid_types) ? $type : self::TYPE_ADJUSTMENT;
    }

    // Obtener los tipos de movimiento
    public static function getMovementTypes() {
        return [
            self::TYPE_ENTRY => 'Entrada',
            self::TYPE_EXIT => 'Salida',
            self::TYPE_ADJUSTMENT => 'Ajuste',
            self::TYPE_SALE => 'Venta',
            self::TYPE_PURCHASE => 'Compra'
        ];
    }

    // Obtener el stock actual de un producto
    public function getCurrentStock($product_id) {
        $query = "SELECT stock FROM products WHERE id = :product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (float)$result['stock'] : 0;
    }
    
    /**
     * Obtiene estadísticas del inventario
     * 
     * @return array Arreglo con las estadísticas del inventario
     */
    public function getInventoryStats() {
        $stats = [
            'total_quantity' => 0,
            'total_products' => 0,
            'low_stock' => []
        ];
        
        try {
            // Obtener el stock total y productos con bajo stock
            $query = "SELECT 
                        p.id,
                        p.name,
                        p.code,
                        p.min_stock,
                        p.stock as current_stock
                      FROM products p
                      WHERE p.stock > 0";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $total_quantity = 0;
            $low_stock = [];
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $total_quantity += $row['current_stock'];
                
                // Verificar si está por debajo del stock mínimo (si está definido)
                if ($row['min_stock'] !== null && $row['current_stock'] <= $row['min_stock']) {
                    $low_stock[] = [
                        'id' => $row['id'],
                        'name' => $row['name'],
                        'code' => $row['code'],
                        'current_stock' => $row['current_stock'],
                        'min_stock' => $row['min_stock']
                    ];
                }
            }
            
            $stats['total_quantity'] = $total_quantity;
            $stats['total_products'] = $stmt->rowCount();
            $stats['low_stock'] = $low_stock;
            
        } catch (PDOException $e) {
            // Registrar el error
            error_log("Error en getInventoryStats: " . $e->getMessage());
        }
        
        return $stats;
    }
}
