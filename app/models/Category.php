<?php
class Category {
    private $conn;
    private $table_name = "categories";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todas las categorías
    public function getAll($page = 1, $per_page = 10) {
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
    
    // Contar el total de categorías
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['total'];
    }

    // Obtener una categoría por ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear una nueva categoría
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                 (name, description, created_at, updated_at) 
                 VALUES (:name, :description, NOW(), NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizar datos
        $name = htmlspecialchars(strip_tags($data['name']));
        $description = htmlspecialchars(strip_tags($data['description'] ?? ''));
        
        // Vincular parámetros
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }

    // Actualizar una categoría
    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                 SET name = :name, 
                     description = :description, 
                     updated_at = NOW() 
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizar datos
        $name = htmlspecialchars(strip_tags($data['name']));
        $description = htmlspecialchars(strip_tags($data['description'] ?? ''));
        
        // Vincular parámetros
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    // Eliminar una categoría
    public function delete($id) {
        // Verificar si hay productos asociados
        $query = "SELECT COUNT(*) as count FROM products WHERE category_id = :category_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['count'] > 0) {
            throw new Exception('No se puede eliminar la categoría porque tiene productos asociados');
        }

        // Si no hay productos asociados, proceder con la eliminación
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    // Verificar si el nombre de la categoría ya existe
    public function nameExists($name, $excludeId = null) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE name = :name";
        $params = [':name' => $name];
        
        if ($excludeId) {
            $query .= " AND id != :id";
            $params[':id'] = $excludeId;
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        
        return $stmt->rowCount() > 0;
    }
}
