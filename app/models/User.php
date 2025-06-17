<?php
class User {
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener usuario por nombre de usuario
    public function getUserByUsername($username) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        
        $username = htmlspecialchars(strip_tags($username));
        $stmt->bindParam(':username', $username);
        
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Obtener usuario por correo electrónico
    public function getUserByEmail($email) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        
        $email = htmlspecialchars(strip_tags($email));
        $stmt->bindParam(':email', $email);
        
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Crear un nuevo usuario
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                (username, password, full_name, email, role) 
                VALUES (:username, :password, :full_name, :email, :role)";
                
        $stmt = $this->conn->prepare($query);
        
        // Sanitizar datos
        $username = htmlspecialchars(strip_tags($data['username']));
        $password = $data['password']; // Ya viene hasheado
        $full_name = htmlspecialchars(strip_tags($data['full_name']));
        $email = htmlspecialchars(strip_tags($data['email']));
        $role = htmlspecialchars(strip_tags($data['role']));
        
        // Vincular valores
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':role', $role);
        
        // Ejecutar consulta
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Actualizar información del usuario
    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                SET full_name = :full_name, email = :email, updated_at = CURRENT_TIMESTAMP 
                WHERE id = :id";
                
        $stmt = $this->conn->prepare($query);
        
        // Sanitizar datos
        $full_name = htmlspecialchars(strip_tags($data['full_name']));
        $email = htmlspecialchars(strip_tags($data['email']));
        
        // Vincular valores
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id', $id);
        
        // Ejecutar consulta
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Cambiar contraseña
    public function changePassword($id, $new_password) {
        $query = "UPDATE " . $this->table_name . " 
                SET password = :password, updated_at = CURRENT_TIMESTAMP 
                WHERE id = :id";
                
        $stmt = $this->conn->prepare($query);
        
        // Hashear la nueva contraseña
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Vincular valores
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':id', $id);
        
        // Ejecutar consulta
        return $stmt->execute();
    }
}
