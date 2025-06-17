<?php
class Product {
    private $conn;
    private $table_name = "products";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todos los productos
    public function getAll($page = 1, $per_page = 10) {
        $offset = ($page - 1) * $per_page;
        
        $query = "SELECT p.*, c.name as category_name 
                 FROM " . $this->table_name . " p 
                 LEFT JOIN categories c ON p.category_id = c.id 
                 ORDER BY p.created_at DESC 
                 LIMIT :offset, :per_page";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':per_page', $per_page, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Contar el total de productos
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['total'];
    }
    
    // Obtener un producto por ID
    public function getById($id) {
        $query = "SELECT p.*, c.name as category_name 
                 FROM " . $this->table_name . " p 
                 LEFT JOIN categories c ON p.category_id = c.id 
                 WHERE p.id = :id 
                 LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Crear un nuevo producto
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                (category_id, code, name, description, price, cost, stock, min_stock) 
                VALUES (:category_id, :code, :name, :description, :price, :cost, :stock, :min_stock)";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizar datos
        $category_id = htmlspecialchars(strip_tags($data['category_id']));
        $code = htmlspecialchars(strip_tags($data['code']));
        $name = htmlspecialchars(strip_tags($data['name']));
        $description = htmlspecialchars(strip_tags($data['description']));
        $price = floatval($data['price']);
        $cost = floatval($data['cost']);
        $stock = intval($data['stock']);
        $min_stock = intval($data['min_stock']);
        
        // Vincular valores
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':cost', $cost);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':min_stock', $min_stock);
        
        // Ejecutar consulta
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }
    
    // Actualizar un producto
    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                 SET category_id = :category_id, 
                     code = :code, 
                     name = :name, 
                     description = :description, 
                     price = :price, 
                     cost = :cost, 
                     stock = :stock, 
                     min_stock = :min_stock, 
                     updated_at = CURRENT_TIMESTAMP 
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizar datos
        $category_id = htmlspecialchars(strip_tags($data['category_id']));
        $code = htmlspecialchars(strip_tags($data['code']));
        $name = htmlspecialchars(strip_tags($data['name']));
        $description = htmlspecialchars(strip_tags($data['description']));
        $price = floatval($data['price']);
        $cost = floatval($data['cost']);
        $stock = intval($data['stock']);
        $min_stock = intval($data['min_stock']);
        
        // Vincular valores
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':cost', $cost);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':min_stock', $min_stock);
        $stmt->bindParam(':id', $id);
        
        // Ejecutar consulta
        return $stmt->execute();
    }
    
    // Eliminar un producto
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    // Verificar si un código de producto ya existe
    public function codeExists($code, $exclude_id = null) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE code = :code";
        
        if ($exclude_id) {
            $query .= " AND id != :id";
        }
        
        $query .= " LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':code', $code);
        
        if ($exclude_id) {
            $stmt->bindParam(':id', $exclude_id);
        }
        
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
}
