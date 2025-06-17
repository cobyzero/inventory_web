<?php
class Customer {
    private $conn;
    private $table_name = "customers";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todos los clientes con paginación
    public function getAll($page = 1, $per_page = 15) {
        $offset = ($page - 1) * $per_page;
        
        $query = "SELECT * FROM " . $this->table_name . " 
                 ORDER BY name ASC
                 LIMIT :offset, :per_page";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':per_page', $per_page, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Contar el total de clientes
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['total'];
    }

    // Obtener un cliente por ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear un nuevo cliente
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                 (name, email, phone, address, created_at, updated_at)
                 VALUES (:name, :email, :phone, :address, NOW(), NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        // Limpiar y validar datos
        $name = htmlspecialchars(strip_tags($data['name']));
        $email = isset($data['email']) ? htmlspecialchars(strip_tags($data['email'])) : null;
        $phone = isset($data['phone']) ? htmlspecialchars(strip_tags($data['phone'])) : null;
        $address = isset($data['address']) ? htmlspecialchars(strip_tags($data['address'])) : null;
        
        // Enlazar parámetros
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }

    // Actualizar un cliente
    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                 SET name = :name, email = :email, phone = :phone, 
                     address = :address, updated_at = NOW()
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Limpiar y validar datos
        $name = htmlspecialchars(strip_tags($data['name']));
        $email = isset($data['email']) ? htmlspecialchars(strip_tags($data['email'])) : null;
        $phone = isset($data['phone']) ? htmlspecialchars(strip_tags($data['phone'])) : null;
        $address = isset($data['address']) ? htmlspecialchars(strip_tags($data['address'])) : null;
        
        // Enlazar parámetros
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);
        
        return $stmt->execute();
    }

    // Eliminar un cliente
    public function delete($id) {
        // Primero verificamos si el cliente tiene ventas asociadas
        $query = "SELECT COUNT(*) as count FROM sales WHERE customer_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] > 0) {
            // No permitir eliminar si hay ventas asociadas
            return false;
        }
        
        // Si no hay ventas, proceder a eliminar
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    // Buscar clientes por nombre o email
    public function search($term, $limit = 10) {
        $query = "SELECT id, name, email FROM " . $this->table_name . " 
                 WHERE name LIKE :term OR email LIKE :term
                 ORDER BY name ASC
                 LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $searchTerm = "%$term%";
        $stmt->bindParam(':term', $searchTerm);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Obtener estadísticas de clientes
    public function getStats() {
        $stats = [];
        
        // Total de clientes
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['total_customers'] = $result['total'];
        
        // Clientes nuevos este mes
        $query = "SELECT COUNT(*) as new_this_month FROM " . $this->table_name . " 
                 WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) 
                 AND YEAR(created_at) = YEAR(CURRENT_DATE())";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['new_this_month'] = $result['new_this_month'];
        
        return $stats;
    }
}
