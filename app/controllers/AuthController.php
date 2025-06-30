<?php
require_once 'BaseController.php';

class AuthController extends BaseController {
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            // Aquí deberías validar las credenciales contra la base de datos
            $user = $this->model('User')->getUserByUsername($username);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                
                if ($_SESSION['role'] === 'admin') {
                    redirect('/dashboard');
                } else {
                    redirect('/user');
                }
            } else {
                $error = 'Usuario o contraseña incorrectos';
            }
        }
        
        $this->render('auth/login', ['error' => $error ?? null]);
    }
    
    public function register() {
        $error = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $full_name = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            
            // Validaciones
            if (empty($username) || empty($password) || empty($confirm_password) || empty($full_name) || empty($email)) {
                $error = 'Todos los campos son obligatorios';
            } elseif ($password !== $confirm_password) {
                $error = 'Las contraseñas no coinciden';
            } elseif (strlen($password) < 6) {
                $error = 'La contraseña debe tener al menos 6 caracteres';
            } else {
                $userModel = $this->model('User');
                
                // Verificar si el usuario ya existe
                if ($userModel->getUserByUsername($username)) {
                    $error = 'El nombre de usuario ya está en uso';
                } elseif ($userModel->getUserByEmail($email)) {
                    $error = 'El correo electrónico ya está registrado';
                } else {
                    // Crear el usuario
                    $userData = [
                        'username' => $username,
                        'password' => password_hash($password, PASSWORD_DEFAULT),
                        'full_name' => $full_name,
                        'email' => $email,
                        'role' => 'user'
                    ];
                    
                    if ($userModel->create($userData)) {
                        // Redirigir al login con mensaje de éxito
                        $_SESSION['success_message'] = '¡Registro exitoso! Por favor inicia sesión.';
                        redirect('auth/login');
                    } else {
                        $error = 'Error al crear la cuenta. Por favor, intenta de nuevo.';
                    }
                }
            }
        }
        
        $this->render('auth/register', ['error' => $error]);
    }
    
    public function logout() {
        session_destroy();
        redirect('/auth/login');
    }
}
